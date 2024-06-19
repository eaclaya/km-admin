<?php

namespace App\Jobs;

use App\Models\InvoiceDiscount;
use App\models\Main\Billing;
use App\Models\Main\Client;
use App\Models\Main\ClientPoint;
use App\Models\Main\Account;
use App\Models\Main\Employee;
use App\Models\Main\Product;

use App\Services\ReportProcessServices;
use App\Libraries\Utils;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;

class ExportInvoicesPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ReportProcessServices $reportProcessServices;
    protected string $nameFile;
    protected int $reportProcessId;
    protected array $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reportProcessServices, $nameFile, $reportProcessId, $ids)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->ids = $ids;
        $this->reportProcessServices = $reportProcessServices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportProcessId = $this->reportProcessId;
        $nameFile = $this->nameFile;
        $ids = $this->ids;
//      ----------------------------
        $file = public_path() . '/' . $nameFile;
        $view_pdf = 'invoice_discount.print';
        $dataArr = $this->generateInvoiceReport($ids);
        $this->reportProcessServices->getFilesServices()->appendToPdf($dataArr, $file, $view_pdf);
//        -------------------------
        $this->reportProcessServices->getRepository()->updateReportProcess($reportProcessId);
        return;
    }

    private function generateInvoiceReport($ids): array
    {
        $invoices = InvoiceDiscount::
            whereIn('invoice_id', $ids)
            ->with(['items'])
            ->where('invoice_type_id', 1)
            ->orderBy('invoice_date', 'ASC')
            ->orderBy('invoice_number', 'ASC')
            ->get();

        $clients = Client::whereIn('id', $invoices->pluck('client_id'))
            ->get()->keyBy('id');

        $billings = Billing::where('is_invoice', 1)->whereIn('billing_id', $invoices->pluck('billing_id'))
            ->get()->keyBy('billing_id');

        $accounts = Account::whereIn('id', $invoices->pluck('account_id'))->get()->keyBy('id');

        $clientPoint = ClientPoint::whereIn('account_id', $invoices->pluck('account_id'))
            ->whereIn('invoice_id', $invoices->pluck('invoice_id'))
            ->get()->keyBy('invoice_id');

        $employees = Employee::whereIn('id', $invoices->pluck('employee_id'))->get()->keyBy('id');
        $auxiliaries = Employee::whereIn('id', $invoices->pluck('auxiliar_id'))->get()->keyBy('id');

        $receipts = [];
        foreach ($invoices as $invoice) {
            $client = isset($clients[$invoice->client_id]) ? $clients[$invoice->client_id] : null;
            $billing = isset($billings[$invoice->billing_id]) ? $billings[$invoice->billing_id] : null;
            $account = isset($accounts[$invoice->account_id]) ? $accounts[$invoice->account_id] : null;
            $points = isset($clientPoint[$invoice->sync_invoice_id]) ? $clientPoint[$invoice->sync_invoice_id] : null;
            $employee = isset($employees[$invoice->employee_id]) ? $employees[$invoice->employee_id] : null;
            $auxiliary = isset($auxiliaries[$invoice->auxiliar_id]) ? $auxiliaries[$invoice->auxiliar_id] : null;
            $receipts[] = $this->getReceipt($invoice, $client, $billing, $account, $points, $employee, $auxiliary);
        }
        return $receipts;
    }

    private function getReceipt($invoice, $client, $billing, $account, $points, $employee, $auxiliary): array
    {

        $entityType = ENTITY_PROFORMA;
        if ($invoice->invoice_type_id == INVOICE_TYPE_QUOTE) {
            $entityType = ENTITY_QUOTE;
        }
        if ($invoice->invoice_type_id == INVOICE_TYPE_STANDARD) {
            $entityType = ENTITY_INVOICE;
        }

        if ($entityType == ENTITY_INVOICE) {
            $invoice->points = 0;
            $clientPoint = $points;
            if ($clientPoint) {
                $invoice->points = $clientPoint->points;
            }
        }

        $invoice->invoice_date = Utils::fromSqlDate($invoice->invoice_date);
        $invoice->recurring_due_date = $invoice->due_date; // Keep in SQL form
        $invoice->due_date = Utils::fromSqlDate($invoice->due_date);
        $invoice->start_date = Utils::fromSqlDate($invoice->start_date);
        $invoice->end_date = Utils::fromSqlDate($invoice->end_date);
        $invoice->last_sent_date = Utils::fromSqlDate($invoice->last_sent_date);

        $itemsCount = 0;
        $itemsQty = 0;
        $itemsDiscount = 0;
        $originalTotal = 0;
        if ($invoice->tax_rate1 > 0) {
            $taxRate = (1 + ($invoice->tax_rate1 / 100));
            foreach ($invoice->items as &$item) {
                if (isset($client)) {
                    $product = Product::where('account_id', $account->id)->where('product_key', $item->product_key)->first();
                    if (isset($product)) {
                        $item->original_price = $product->price / $taxRate;
                        $item->cost = $item->cost / (1 + ($invoice->tax_rate1 / 100));
                        $item->product = $product;
                        $itemsDiscount +=  ($item->qty * ($item->original_price - $item->cost));
                        $originalTotal += ($item->qty * $item->original_price);
                    }
                }
                $itemsCount++;
                $itemsQty += intval($item->qty);
            }
        }
        $invoice->original_total = number_format($originalTotal, 2, '.', ',');
        $invoice->items_discount = number_format($itemsDiscount, 2, '.', ',');
        $invoice->items_count = $itemsCount;
        $invoice->items_qty = $itemsQty;

        $data = [
            'account' => $account,
            'entityType' => $entityType,
            'invoice' => $invoice,
            'title' => trans("texts.edit_{$entityType}"),
            'client' => $client,
            'employee' => $employee,
            'auxiliary' => $auxiliary
        ];

        if ($entityType == ENTITY_INVOICE) {
            $data['billing'] = $billing;
        }
        return $data;
    }
}
