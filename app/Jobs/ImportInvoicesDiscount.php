<?php

namespace App\Jobs;

use App\models\Main\Billing;
use App\Models\Main\Client;
use App\Models\Invoice;
use App\Models\Main\OrganizationCompany;

use App\Models\Main\Account;
use App\Models\ReportProcess;

use Illuminate\Support\Facades\DB;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;

class ImportInvoicesDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile, $reportProcessId, $file, $offset, $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $file, $offset, $limit)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->file = $file;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dump('inicio a procesar con la siguiente tienda');
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $stores = $this->stores;
        $from_date = $this->from_date;
        $to_date = $this->to_date;
        $filter = $this->filter;

//      ----------------------------
        $file = public_path() . '/' . $nameFile;
        $this->generateInvoiceReport($from_date, $to_date, $stores, $file, $filter);

//        -------------------------

        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int)$reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
        if($finish){
            $updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->updated_at = $updated_at;
            $reportProcess->status = 1;
        }
        $reportProcess->save();
        return;
    }

    private function generateInvoiceReport($startDate, $endDate, $stores, $file, $filter)
    {
        $invoices = Invoice::
            select(['sync_invoice_items_id', 'account_id', 'invoice_number', 'invoice_date', 'amount','billing_id','client_id'])
            ->whereIn('account_id', $stores)
            ->where('invoice_type_id', 1)
            ->where('invoice_date', '>=', $startDate)
            ->where('invoice_date', '<', $endDate)
            ->orderBy('invoice_date', 'ASC')
            ->orderBy('invoice_number', 'ASC')
            ->get();

        $billing = Billing::whereIn('billing_id', $invoices->pluck('billing_id'))->pluck('cai','billing_id');
        $clients = Client::whereIn('id', $invoices->pluck('client_id'))->select(['id','name','vat_number'])->get()->keyBy('id');
        $accounts = Account::whereIn('id', $stores)->select(['id','name','organization_company_id'])->get()->keyBy('id');
        $companyOrg = OrganizationCompany::pluck('name', 'id');

        $fpu = fopen($file, 'a+');

        foreach ($invoices as $invoice) {
            $cai = isset($billing[$invoice->billing_id]) ? $billing[$invoice->billing_id] : '';
            if($filter == 1 && empty($cai)){
                continue;
            }elseif($filter == 2 && !empty($cai)){
                continue;
            }
            $displayData = [
                $invoice->sync_invoice_items_id,
                isset($companyOrg[$accounts[$invoice->account_id]->organization_company_id]) ? $companyOrg[$accounts[$invoice->account_id]->organization_company_id] : '',
                isset($accounts[$invoice->account_id]) ? $accounts[$invoice->account_id]->name : '',
                isset($clients[$invoice->client_id]) ? $clients[$invoice->client_id]->name : '',
                isset($clients[$invoice->client_id]) ? $clients[$invoice->client_id]->vat_number : '',
                $invoice->invoice_number,
                $cai,
                $invoice->invoice_date,
                $invoice->amount
            ];
            fputcsv($fpu, $displayData, ';');
        }

        fclose($fpu);
    }
}
