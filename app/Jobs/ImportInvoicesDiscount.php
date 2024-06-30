<?php

namespace App\Jobs;

use App\Models\InvoiceDiscount;
use App\Models\Main\Invoice;
use App\Repositories\ReportProcessRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportInvoicesDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ReportProcessRepository $reportProcessRepo;
    protected int $reportProcessId;
    protected string $filePath;
    protected array $chunk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReportProcessRepository $reportProcessRepo, int $reportProcessId, string $filePath, array $chunk)
    {
        $this->reportProcessId = $reportProcessId;
        $this->filePath = $filePath;
        $this->chunk = $chunk;
        $this->reportProcessRepo = $reportProcessRepo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dump('inicio a procesar con la siguiente tienda');
        $reportProcessId = $this->reportProcessId;
        $filePath = $this->filePath;
        $chunk = $this->chunk;

//      ----------------------------
        $this->processImportInvoices($filePath, $chunk);
//        -------------------------
        $this->reportProcessRepo->updateReportProcess($reportProcessId);
        return;
    }

    private function processImportInvoices($filePath, $chunk): void
    {
        $csv = new \ParseCsv\Csv();
        $csv->encoding('ISO-8859-1','UTF-8');
        $csv->limit = $chunk['limit'];
        $csv->offset = $chunk['offset'];
        $csv->auto($filePath);
        $data = collect($csv->data);
        $invoices = Invoice::query()->whereIn('id', $data->pluck('invoice_id')->toArray())->with('items')->get()->keyBy('id');
        $invoicesDiscount = InvoiceDiscount::query()->whereIn('invoice_id', $data->pluck('invoice_id')->toArray())->with('items')->get()->keyBy('invoice_id');

        $dataInvoices = [];
        $dataInvoiceItems = [];
        dump('inicio el foreach');
        foreach ($data as $discount) {
            $currentsInvoices = isset($invoices[$discount['invoice_id']]) ? $invoices[$discount['invoice_id']] : null;
            if(is_null($currentsInvoices)){
               continue;
            }
            $issetDiscount = isset($invoicesDiscount[$discount['invoice_id']]) ? $invoicesDiscount[$discount['invoice_id']] : null;
            if(is_null($issetDiscount)){
                if($this->checkInvoiceDifference($currentsInvoices->amount, $discount['total'])){
                    continue;
                }
                $calculation = $this->typeCalculationAndPercentage($currentsInvoices->amount, $discount['total']);
                $isSuma = $calculation['isSuma'];
                $percentageChange = $calculation['percentageChange'];
                $discountedItems = $this->getDiscountedItems($currentsInvoices->items, $isSuma, $percentageChange);
                $invoice = $this->convertInvoicesToArray($currentsInvoices, $discount['total']);
                $dataInvoices[] = $invoice;
                $dataInvoiceItems = array_merge($dataInvoiceItems, $discountedItems);
            }else{
                if($this->checkInvoiceDifference($issetDiscount->amount, $discount['total'])){
                    continue;
                }
                $calculation = $this->typeCalculationAndPercentage($issetDiscount->amount, $discount['total']);
                $isSuma = $calculation['isSuma'];
                $percentageChange = $calculation['percentageChange'];
                foreach($issetDiscount->items as &$item){
                    if($isSuma){
                        $item->cost += $item->cost * ($percentageChange / 100);
                    }else{
                        $item->cost -= $item->cost * ($percentageChange / 100);
                    }
                    $item->save();
                }
                $issetDiscount->total = $discount['total'];
                $issetDiscount->amount = $discount['total'];
                $issetDiscount->save();
            }
        }
        dump('salgo del foreach');
        if(count($dataInvoices) > 0){
            $this->insertInvoices($dataInvoices);
            if(count($dataInvoiceItems) > 0) {
                $this->insertInvoiceItems($dataInvoiceItems);
            }
        }
    }

    private function checkInvoiceDifference($invoicesTotal, $total): bool
    {
        return $invoicesTotal == $total;
    }
    private function typeCalculationAndPercentage($invoicesTotal, $total): array
    {
        $isSuma = false;
        if($total > $invoicesTotal){
            $percentageChange = (($total - $invoicesTotal) / $invoicesTotal) * 100;
            $isSuma = true;
        }else{
            $percentageChange = (($invoicesTotal - $total) / $invoicesTotal) * 100;
        }
        return [
            'isSuma' => $isSuma,
            'percentageChange' => $percentageChange
        ];
    }
    private function getDiscountedItems($invoicesItems, $isSuma, $percentageChange): array
    {
        return $invoicesItems->map(function ($newItem) use ($isSuma, $percentageChange) {
            $newItem = $newItem->getAttributes();
            if($isSuma){
                $newItem['cost'] += $newItem['cost'] * ($percentageChange / 100);
            }else{
                $newItem['cost'] -= $newItem['cost'] * ($percentageChange / 100);
            }
            $newItem['invoice_items_id'] = $newItem['id'];
            unset($newItem['id']);
            $newItem['created_at'] = isset($newItem['created_at']) ? date('Y-m-d H:i:s', strtotime($newItem['created_at'])) : null;
            $newItem['updated_at'] = isset($newItem['updated_at']) ? date('Y-m-d H:i:s', strtotime($newItem['updated_at'])) : null;
            $newItem['deleted_at'] = isset($newItem['deleted_at']) ? date('Y-m-d H:i:s', strtotime($newItem['deleted_at'])) : null;
            return $newItem;
        })->toArray();
    }
    private function convertInvoicesToArray($invoice, $total): array
    {
        $invoice = $invoice->toArray();
        $invoice['total'] = $total;
        $invoice['amount'] = $total;
        $invoice['invoice_id'] = $invoice['id'];
        unset($invoice['id']);
        unset($invoice['items']);
        $invoice['created_at'] = date('Y-m-d H:i:s', strtotime($invoice['created_at']));
        $invoice['updated_at'] = date('Y-m-d H:i:s', strtotime($invoice['updated_at']));
        return $invoice;
    }
    private function insertInvoices($dataInvoices): void
    {
        $chunks = array_chunk($dataInvoices, 250);
        foreach ($chunks as $chunk) {
            DB::connection('main')->table('invoices_discount')->insert($chunk);
        }
    }
    public function insertInvoiceItems($dataInvoiceItems): void
    {
        $groupedInvoiceItems = collect($dataInvoiceItems)->groupBy('invoice_id')->toArray();
        foreach ($groupedInvoiceItems as $invoiceId => $items) {
            $chunks = array_chunk($items, 25);
            foreach ($chunks as $chunk) {
                DB::connection('main')->table('invoice_items_discount')->insert($chunk);
            }
        }
    }
}
