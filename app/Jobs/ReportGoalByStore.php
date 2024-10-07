<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Carbon\Carbon;
use DB;
use App\Models\ReportProcess;
use App\Models\CompanyZones;
use App\Models\Goal;
use App\Models\Route;

class ReportGoalByStore extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $nameFile, $reportProcessId, $stores, $from_date, $to_date, $type, $limitDay, $dayNumber, $dayOffset;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $stores, $from_date, $to_date, $type, $limitDay, $dayNumber, $dayOffset)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->stores = $stores;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->type = $type;
        $this->limitDay = $limitDay;
        $this->dayNumber = $dayNumber;
        $this->dayOffset = $dayOffset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$result = [];
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $stores = $this->stores;
        $from_date = $this->from_date;
        $to_date = $this->to_date;
        $type = $this->type;
        $limitDay = $this->limitDay;
        $dayNumber = $this->dayNumber;
        $dayOffset = $this->dayOffset;
        $zones = CompanyZones::pluck('name','id');

        $exception = null;
        try {
            $routes = Route::whereNull('deleted')->whereNotNull('seller_id')->pluck('seller_id')->toArray();
            $invoices = DB::table('invoices')->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                    ->join('clients', 'clients.id', '=', 'invoices.client_id')
                    ->join('goals', 'goals.id', '=', 'accounts.goal_id')
                    ->select(
                        DB::raw('SUM(invoices.total) as total'), 
                        DB::raw('SUM(IF(invoices.client_type = "Normal", invoices.oil, 0)) as oil'), 
                        DB::raw('SUM(invoices.amount) as sale_amount'),
                        'accounts.name as account', 'accounts.id as account_id', 'invoices.invoice_date', 'clients.type',
                        'goals.total as goal', 'accounts.name as account', 'accounts.company_zones_id as zone'
                    )
                    ->whereIn('invoices.account_id', $stores)
                    ->where('invoices.account_id', '<>', 6)
                    ->where('invoice_type_id', 1)
                    ->whereDate('invoices.invoice_date', '>=', $from_date)
                    ->whereDate('invoices.invoice_date', '<=', $to_date)
                    ->groupBy('invoices.account_id')
                    ->whereNotIn('invoices.employee_id', $routes)->get();

            $refundsData = DB::table('refunds')
                        ->whereIn('refunds.account_id', $stores)
                        ->whereDate('refunds.refund_date', '>=', $from_date)
                        ->whereDate('refunds.refund_date', '<=', $to_date)
                        ->select('account_id', DB::raw('SUM(total_refunded) as total_refunded_sum'))
                        ->groupBy('account_id')
                        ->get();

            $refunds = [];

            foreach ($refundsData as $refund) {
                $refunds[$refund->account_id] = $refund->total_refunded_sum;
            }

            foreach($invoices as $invoice){
                $result[$invoice->account_id] = $invoice;
	            $goal = Goal::where('account_id', $invoice->account_id)->whereDate('created_at', '<=', $to_date)->orderBy('created_at', 'DESC')->first();
                if(!$goal || !$result[$invoice->account_id]){
                    unset($result[$invoice->account_id]);
                    continue;
                }
                $item = DB::table('invoice_items')->join('products', 'invoice_items.product_id', '=', 'products.id')
                        ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                        ->join('clients', 'clients.id', '=', 'invoices.client_id')
                        ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
                        ->select(DB::raw('SUM(IF(categories.name = "LUBRICANTES" OR categories.name = "ACEITES", invoice_items.product_cost * invoice_items.qty, 0)) as oil_cost'), 
                        DB::raw('SUM(IF(categories.name != "LUBRICANTES" AND categories.name != "ACEITES", invoice_items.cost * invoice_items.qty, 0)) as total_cost'), 
                        DB::raw('SUM(IF(categories.name = "LUBRICANTES" OR categories.name = "ACEITES", IF(clients.type <> "Normal", invoice_items.cost * invoice_items.qty, 0), 0)) as oil_amount'), 'invoice_items.qty', 'products.category_id')
                        ->whereDate('invoices.invoice_date', '>=', $from_date)
                        ->whereDate('invoices.invoice_date', '<=', $to_date)
                        ->where('invoices.account_id', '=', $invoice->account_id)
                        ->where('invoices.invoice_type_id', '=', 1)
                        ->whereNull('invoice_items.deleted_at')
                        ->first();
                        
                $goal = floatval($goal->total);
					$result[$invoice->account_id]->zone  = isset($zones[$invoice->zone]) ? $zones[$invoice->zone] : 'Sin Asignar';
					$result[$invoice->account_id]->refunds  = isset($refunds[$invoice->account_id])?$refunds[$invoice->account_id]:0;
					$result[$invoice->account_id]->oil_wholesaler  =   floatval($item->oil_amount) ;
					$result[$invoice->account_id]->total_oil  = floatval($invoice->oil);
					$result[$invoice->account_id]->goal = $type == '1' ?  floatval($goal) : floatval($goal) * 2;
					$result[$invoice->account_id]->sale_amount = floatval($item->total_cost);
					$result[$invoice->account_id]->total_sale = floatval($item->total_cost)  + floatval($result[$invoice->account_id]->total_oil);
					$result[$invoice->account_id]->from_date = $from_date;
					$result[$invoice->account_id]->to_date = $to_date;
					$result[$invoice->account_id]->goal = $result[$invoice->account_id]->goal > 0 ? $result[$invoice->account_id]->goal : 1;
					$result[$invoice->account_id]->total_goal = number_format(($result[$invoice->account_id]->total_sale/$result[$invoice->account_id]->goal) * 100, 2, '.', '');
					$result[$invoice->account_id]->goal_daily = round($result[$invoice->account_id]->goal/$limitDay, 2);
					$result[$invoice->account_id]->pond_cash = round(($result[$invoice->account_id]->total_sale/$dayNumber) * $limitDay, 2);
					$result[$invoice->account_id]->pond_avg = round(($result[$invoice->account_id]->pond_cash/$result[$invoice->account_id]->goal) * 100, 2);
					$result[$invoice->account_id]->amount_daily = round(($result[$invoice->account_id]->goal - $result[$invoice->account_id]->total_sale)/$dayOffset, 2);
					$result[$invoice->account_id]->goal_ideal = round((100/$limitDay) * $dayNumber, 2);
					$result[$invoice->account_id]->deficit_avg = $result[$invoice->account_id]->goal_ideal - floatval($result[$invoice->account_id]->total_goal);
					$result[$invoice->account_id]->amount_ideal = $result[$invoice->account_id]->goal_daily * $dayNumber;
					$result[$invoice->account_id]->deficit_cash = $result[$invoice->account_id]->amount_ideal - $result[$invoice->account_id]->total_sale;
					$result[$invoice->account_id]->deficit_plus_goal_daily = $result[$invoice->account_id]->deficit_cash + $result[$invoice->account_id]->goal_daily;
            }
            
            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');

            foreach($result as $re){
                $fields = [
                    'account' => $re->account,
                    'zone'  => $re->zone,
                    'sale_amount' => $re->sale_amount,
                    'refunds' => $re->refunds,
                    'oil_wholesaler'  =>  $re->oil_wholesaler,
                    'total_oil'  => $re->total_oil,
                    'total_sale' => $re->total_sale,
                    'goal' => $re->goal,
                    'total_goal' => $re->total_goal,
                    'goal_daily' => $re->goal_daily,
                    'pond_cash' => $re->pond_cash,
                    'pond_avg' => $re->pond_avg,
                    'amount_daily' => $re->amount_daily,
                    'goal_ideal' => $re->goal_ideal,
                    'deficit_avg' => $re->deficit_avg,
                    'amount_ideal' => $re->amount_ideal,
                    'deficit_cash' => $re->deficit_cash,
                    'deficit_plus_goal_daily' => $re->deficit_plus_goal_daily,
                    'from_date' => $re->from_date,
                    'to_date' => $re->to_date
                ];
                fputcsv($fp, $fields, ';');
            }
            fclose($fp);

        }catch (\Exception $e){
            $exception = $e;
            dump($e->getMessage());
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
