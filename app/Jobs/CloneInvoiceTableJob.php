<?php

namespace App\Jobs;

use App\Models\CloningControl;
use Illuminate\Support\Facades\DB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

class CloneInvoiceTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fromDate, $toDate, $accountId, $cloneId;

    /**
     * Create a new job instance.
     */
    public function __construct($fromDate, $toDate, $accountId, $cloneId)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->accountId = $accountId;
        $this->cloneId = $cloneId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fromDate = $this->fromDate;
        $toDate = $this->toDate;
        $accountId = $this->accountId;
        $cloneId = $this->cloneId;
        $invoices = DB::connection('main')
            ->table('invoices')
            ->where('account_id', $accountId)
            ->where('is_sync', 0)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();
        $invoiceToInsert = $invoices->map(function ($invoice) {
            $invoiceArray = (array) $invoice;
            $invoiceArray['sync_invoice_id'] = $invoiceArray['id'];
            unset($invoiceArray['id']);
            unset($invoiceArray['is_sync']);
            if ($invoiceArray['invoice_date'] == '0000-00-00') {
                $invoiceArray['invoice_date'] = null;
            }
            if ($invoiceArray['last_payment_date'] == '0000-00-00') {
                $invoiceArray['last_payment_date'] = null;
            }
            return $invoiceArray;
        })->toArray();
        $chunks = array_chunk($invoiceToInsert, 500);

        foreach ($chunks as $chunk) {
            DB::connection('mysql')->table('invoices')->insert($chunk);
            DB::connection('main')->table('invoices')
                ->whereIn('id', collect($chunk)->pluck('sync_invoice_id'))
                ->update(['is_sync' => 1]);
        }
        $invoiceItems = DB::connection('main')->table('invoice_items')->whereIn('invoice_id', $invoices->pluck('id'))->get();
        $groupedInvoiceItems = $invoiceItems->groupBy('invoice_id');
        foreach ($invoices as $invoice) {
            if (isset($groupedInvoiceItems[$invoice->id])) {
                $invoiceItemsToInsert = $groupedInvoiceItems[$invoice->id]->map(function ($invoiceItem) {
                    $invoiceItemArray = (array) $invoiceItem;
                    $invoiceItemArray['sync_invoice_items_id'] = $invoiceItemArray['id'];
                    unset($invoiceItemArray['id']);
                    return $invoiceItemArray;
                })->toArray();
                $iITI = array_chunk($invoiceItemsToInsert, 25);
                foreach ($iITI as $chunkIITI) {
                    DB::connection('mysql')->table('invoice_items')->insert($chunkIITI);
                }
            }
        }
        $reportProcess = CloningControl::find($cloneId);
        $reportProcess->is_completed = 1;
        $reportProcess->save();
        return;
    }
}
