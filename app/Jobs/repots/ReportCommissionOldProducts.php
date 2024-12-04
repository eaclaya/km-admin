<?php

namespace App\Jobs;

use App\Models\Main\Account;
use App\Models\Main\Employee;
use App\Models\Main\Invoice;
use App\Models\Main\ProductTracking;
use App\Models\Main\ReportProcess;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReportCommissionOldProducts extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $nameFile;

    protected $reportProcessId;

    protected $stores;

    protected $from_date;

    protected $to_date;

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
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $storeIds = $this->stores;
        $from_date = $this->from_date;
        $to_date = $this->to_date;

        // Obtén las instancias de Account basadas en los IDs
        $stores = Account::whereIn('id', $storeIds)->get();
        $today = Carbon::now();

        // Calcula las fechas de la quincena y seis meses atrás
        if ($today->day <= 15) {
            $first_day_of_fortnight = $today->copy()->startOfMonth();
            $last_day_of_fortnight = $first_day_of_fortnight->copy()->addDays(14);
        } else {
            $first_day_of_fortnight = $today->copy()->startOfMonth()->addDays(15);
            $last_day_of_fortnight = $today->copy()->endOfMonth();
        }

        $six_months_ago = $last_day_of_fortnight->copy()->subMonths(6);

        $from_date = ! empty($from_date) ? $from_date : $first_day_of_fortnight->format('Y-m-d');
        $to_date = ! empty($to_date) ? $to_date : $last_day_of_fortnight->format('Y-m-d');

        $exception = null;

        try {
            $file = public_path().'/'.$nameFile;
            $fp = fopen($file, 'a+');

            foreach ($stores as $account) {
                $employees = Employee::where('account_id', $account->id)
                    ->where('is_seller', true)
                    ->where('enabled', true)
                    ->select('id', 'account_id', 'first_name', 'last_name')
                    ->get();

                // Consulta principal para facturas
                $invoices_query = Invoice::join('invoice_items as ii', 'ii.invoice_id', '=', 'invoices.id')
                    ->whereNull('ii.deleted_at')
                    ->whereIn('invoices.employee_id', $employees->pluck('id'))
                    ->where('invoices.account_id', $account->id)
                    ->where('invoices.invoice_type_id', 1)
                    ->where('invoices.invoice_status_id', 6)
                    ->whereBetween('invoices.created_at', [$from_date, $to_date])
                    ->select(
                        'invoices.id as invoice_id',
                        'invoices.public_id',
                        'invoices.invoice_number',
                        'invoices.employee_id',
                        'ii.product_id',
                        'ii.product_key',
                        DB::raw('MIN(ii.created_at) as first_created_at'),
                        'ii.cost',
                        'ii.qty',
                        DB::raw('ii.cost * ii.qty as total_cost')
                    )
                    ->groupBy('ii.product_key');
                $invoices = $invoices_query->get();

                // Consulta de productos que no deben incluirse
                $products_t = ProductTracking::whereDate('products_tracking.created_at', '>', $six_months_ago)
                    ->whereDate('products_tracking.created_at', '<', $from_date)
                    ->where('reason', '!=', 'Cantidad rebajada en factura')
                    ->where('products_tracking.final_account_id', $account->id)
                    ->groupBy('products_tracking.product_key')
                    ->whereIn('products_tracking.product_key', $invoices->pluck('product_key'))
                    ->pluck('products_tracking.product_key');

                $products_u = ProductTracking::whereDate('products_tracking.created_at', '>', $from_date)
                    ->whereDate('products_tracking.created_at', '<', $to_date)
                    ->where('reason', '!=', 'Cantidad rebajada en factura')
                    ->where('products_tracking.transaction_type', '!=', 'invoice')
                    ->where('products_tracking.final_account_id', $account->id)
                    ->whereIn('products_tracking.product_key', $invoices->pluck('product_key'))
                    ->groupBy('products_tracking.product_key')
                    ->whereNotIn('products_tracking.product_key', $products_t)
                    ->pluck('products_tracking.product_key');

                $no_van = $products_t->merge($products_u)->unique();

                $invoicess = $invoices_query->whereNotIn('product_key', $no_van);

                $invoices_old = Invoice::join('invoice_items as ii', 'ii.invoice_id', '=', 'invoices.id')
                    ->whereNull('ii.deleted_at')
                    ->whereIn('invoices.employee_id', $employees->pluck('id'))
                    ->where('invoices.account_id', $account->id)
                    ->whereIn('ii.product_key', $invoicess->pluck('product_key'))
                    ->where('invoices.invoice_type_id', 1)
                    ->where('invoices.invoice_status_id', 6)
                    ->whereDate('invoices.created_at', '>', $six_months_ago)
                    ->whereDate('invoices.created_at', '<', $from_date)
                    ->groupBy('ii.product_key')
                    ->select('ii.product_key', 'ii.created_at');
                $invoices_old = $invoices_old->get();

                $valors = $invoicess->whereNotIn('product_key', $invoices_old->pluck('product_key'))->get();

                foreach ($valors as $valor) {
                    $result = [
                        $account->name ?? '',
                        $valor->employee->first_name.' '.$valor->employee->last_name,
                        $from_date.' al '.$to_date,
                        $valor->product_key,
                        $valor->first_created_at,
                        $valor->invoice_number,
                        $valor->qty,
                        $valor->cost,
                        $valor->total_cost,
                        $valor->total_cost * 0.04,
                    ];
                    fputcsv($fp, $result, ';');
                }
            }

            fclose($fp);
        } catch (\Exception $e) {
            $exception = $e;
            dump($e);
        }

        // Actualiza el estado del proceso en la base de datos
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int) $reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;

        if (! is_null($exception)) {
            $reportProcess->exception .= substr(trim($exception), 0, 200).'*--*';
            $reportProcess->status = 2;
        }

        if ($finish) {
            $updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->updated_at = $updated_at;
            if ($reportProcess->status !== 2) {
                $reportProcess->status = 1;
            }
        }

        $reportProcess->save();
    }
}
