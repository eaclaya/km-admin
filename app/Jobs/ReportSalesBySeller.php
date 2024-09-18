<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon\Carbon;
use DB;
use Utils;

use App\Models\Product;
use App\Models\CountTotalProductKey;
use App\Models\CountTotalRelationId;
use App\Models\ReportProcess;
use App\Models\Account;
use App\Models\Employee;
use App\Models\CompanyZones;

class ReportSalesBySeller extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $nameFile, $reportProcessId, $stores, $from_date, $to_date;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $stores, $from_date, $to_date)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->stores = $stores;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
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
        // dump($stores);
        $from_date = $this->from_date;
        $to_date = $this->to_date;
        // dump('aca ya se leyeron las variable');

        $exception = null;
        try {
            $zones = CompanyZones::pluck('name','id');
            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('employees', 'invoices.employee_id', '=', 'employees.id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(
                        'employees.first_name',
                        'employees.last_name',
                        'invoices.amount',
                        'invoices.total',
                        'invoices.commission',
                        'invoices.invoice_date',
                        'invoices.last_payment_date',
                        'invoices.invoice_number',
                        'accounts.name as account',
                        'employees.zone',
                        'invoices.balance',
                        'invoices.invoice_status_id',
                        'employees.profile',
                        'invoices.id',
                        'clients.name as client',
                        'clients.company_name',
                        'clients.work_phone',
                        'clients.phone',
                        'invoices.in_transit',
                        'invoices.is_credit',
                        DB::raw('SUM(invoice_items.product_cost * invoice_items.qty) as total_cost')
                )
                ->where('accounts.exclude', 0)
                ->where('invoices.invoice_type_id', 1)
                ->whereIn('invoices.account_id', $stores)
                ->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoice_items.invoice_id')->get();

            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');

            foreach($invoices as $invoice){
                $fields = [
                    'employee' => $invoice->first_name.' '.$invoice->last_name,
                    'account' => $invoice->account,
                    'zone' => (isset($invoice->zone) && isset($zones[$invoice->zone])) ? $zones[$invoice->zone] : 'Sin Asignar',
                    'client' => $invoice->company_name??$invoice->client,
                    'phone' => $invoice->work_phone ? $invoice->work_phone : $invoice->phone,
                    'balance' => $invoice->balance,
                    'amount' => $invoice->amount,
                    'total_cost' => $invoice->total_cost,
                    'invoice_status' => $invoice->in_transit ? 'En transito' : ($invoice->is_credit ? 'Credito' : 'Contado'),
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'payment_date' => $invoice->last_payment_date,
                    'commission' => $invoice->commission,
                    'profile' => $invoice->profile
                ];
                fputcsv($fp, $fields, ';');
            }

            fclose($fp);
        }catch (\Exception $e){
            $exception = $e;
            dump($e);
        }
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int)$reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
        if(!is_null($exception)){
            $reportProcess->exception .= substr(trim($exception),0,200) . '*--*';
            $reportProcess->status = 2;
        }
        if($finish){
            $updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->updated_at = $updated_at;
            if($reportProcess->status !== 2){
                $reportProcess->status = 1;
            }
        }
        $reportProcess->save();
        return;
    }
}
