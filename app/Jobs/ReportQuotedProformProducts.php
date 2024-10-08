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

use App\Models\Main\Product;
use App\Models\Main\CountTotalProductKey;
use App\Models\Main\CountTotalRelationId;
use App\Models\Main\ReportProcess;
use App\Models\Main\Account;
use App\Models\Main\Employee;

class ReportQuotedProformProducts extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $nameFile, $reportProcessId, $stores, $from_date, $to_date, $invoiceType;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $stores, $from_date, $to_date, $invoiceType)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->stores = $stores;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->invoiceType = ($invoiceType == 'quoted') ? 2 : 3;
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
        $stores = $this->stores;
        $from_date = $this->from_date;
        $to_date = $this->to_date;
        $invoiceType = $this->invoiceType;

        $exception = null;
        try {
            $products = Product::select('id', 'product_key', 'qty', 'cost', 'relation_qty')->where('account_id', 17)->get()->keyBy('product_key');

            $accountExclude = Account::select('id', 'name')
                ->where('exclude', '>', 0)
                ->get()->keyBy('id')->toArray();
            $accountExclude = array_keys($accountExclude);

            $invoices = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->select('products.product_key', 'products.notes', 'products.qty as availableQty', 'products.vendor_id', 'products.updated_at', 'invoices.account_id', 'invoices.invoice_number', 'invoices.invoice_date', 'invoices.employee_id', 'invoices.in_transit', 'invoices.is_credit', 'invoice_items.qty', 'invoice_items.qty_refunded', 'invoice_items.cost as price',  'invoice_items.product_cost as cost','clients.name', 'products.relation_id')
                ->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('invoices.invoice_type_id', $invoiceType)->whereIn('invoices.account_id', $stores)->get();

            $employees = Employee::all()->keyBy('id');

            $relationIdsKeys = array_keys(collect($invoices)->keyBy('relation_id')->toArray());

            $relatedProducts = CountTotalRelationId::select('relation_id', 'qty')
                ->get()->keyBy('relation_id');

            $sales = DB::table('invoice_items')
                            ->whereDate('invoice_items.created_at', '>=', $from_date)
                            ->whereDate('invoice_items.created_at', '<', $to_date)
                            ->where('invoice_items.invoice_type_id', 1)
                            ->whereNotIn('invoice_items.account_id', $accountExclude)
                            ->join('products', 'products.id', '=', 'invoice_items.product_id')
                            ->whereIn('products.relation_id', $relationIdsKeys)
                            ->select('products.relation_id', DB::raw('SUM(invoice_items.qty) as total'))
                            ->groupBy('products.relation_id')
                            ->get();

            // dump('aca ya se consultaron las invoices');

            $sales = collect($sales)->keyBy('relation_id');
            // dump('aca ya se leyeron las variable');
            // dump('aca se abrira el arcivo');
            $file = public_path() . '/' . $nameFile;
            // dump('file');
            // dump($file);
            $fp = fopen($file, 'a+');
            // dump('aca ya se abrio el arcivo');

            foreach($invoices as $invoice){
                $item = [
                    'id' => $invoice->invoice_number,
                    'name' => $invoice->name,
                    'product_key'=>$invoice->product_key,
                    'product' => $invoice->notes,
                    'product_cost' => $invoice->cost,
                    'price' => $invoice->price,
                    'qty' => $invoice->qty,
                    'product_qty' => $invoice->availableQty,
                    'qtyInWarehouse' => isset($products[$invoice->product_key]) ? $products[$invoice->product_key]->qty : 0,
                    'relation_qty' => isset($products[$invoice->product_key]) ? $products[$invoice->product_key]->relation_qty : 0,
                    'relation_qty_global' => isset($relatedProducts[$invoice->relation_id]) ? $relatedProducts[$invoice->relation_id]->qty : 0,
                    'relation_sales' => isset($sales[$invoice->relation_id]) ? $sales[$invoice->relation_id]->total : 0,
                    'employee' => isset($employees[$invoice->employee_id]) ? ($employees[$invoice->employee_id]->first_name." ".$employees[$invoice->employee_id]->last_name ) : '',
                    'profile' => isset($employees[$invoice->employee_id]) ? ($employees[$invoice->employee_id]->profile ) : '',
                ];
                fputcsv($fp, $item, ';');
            }
            // dump('aca ya se lleno el archivo');
            fclose($fp);
            // dump('aca ya se cerro el archivo');
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
