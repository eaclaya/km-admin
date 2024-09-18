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
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ReportProcess;
use App\Models\Account;
use App\Models\Stock;
use App\Models\InvoiceItem;

class ReportInputEntries extends Job implements ShouldQueue, SelfHandling
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
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $stores = $this->stores;
        $from_date = $this->from_date;
        $to_date = $this->to_date;

        $exception = null;
        try {
            $productsInWarehouse = Product::select('product_key', 'qty')->where('account_id', 17)->get()->keyBy('product_key');

            $accounts = Account::whereIn('id', $stores)->pluck('name', 'id');
            $_products = array();

            foreach($stores as $item){
                $_productss = Product::select('product_key', DB::raw('SUM(qty) as qty'))->where('account_id', $item)->groupBy('product_key')->get()->keyBy('product_key');
                $_products[$item] = $_productss;
            }

            $purchasesId = Purchase::select('id')->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->whereIn('account_id', $stores)->get();
            $purchaseItems = PurchaseItem::whereIn('purchase_id',$purchasesId)->get();
            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');
            foreach($purchaseItems as $purchaseItem){
                $concatenated_costs = '';
                $price_payed = InvoiceItem::join('payments as py', 'py.invoice_id', '=', 'invoice_items.invoice_id')
                        ->select('invoice_items.cost')
                        ->where('invoice_items.account_id', $purchaseItem->account_id)
                        ->where('invoice_items.product_key', $purchaseItem->product_key)
                        ->where('py.created_at', '>=', $purchaseItem->created_at->toDateTimeString())
                        ->distinct()
                        ->get();
                $concatenated_costs = $price_payed->pluck('cost')->implode(', ');
                $completed = isset($purchaseItem->purchase->completed)?$purchaseItem->purchase->completed:'';
                if(isset($completed)){
                    $completed = ($completed == 1) ? 'Completada' : 'Pendiente';
                }else{
                    $completed = '';
                }
                $_result = [
                    'created_at' => $purchaseItem->created_at,
                    'account' => isset($accounts[$purchaseItem->account_id])?$accounts[$purchaseItem->account_id]:'',
                    'proveedor' => isset($purchaseItem->purchase->provider->name)?$purchaseItem->purchase->provider->name:'',
                    'complete' => $completed,
                    'type' => 'Compra',
                    'product_key' => $purchaseItem->product_key,
                    'notes' => $purchaseItem->notes,
                    'qty_before' => $purchaseItem->qty_before,
                    'qty' => $purchaseItem->qty,
                    'qty_after' => $purchaseItem->qty_after,
                    'qtyInWarehouse' => isset($productsInWarehouse[$purchaseItem->product_key]) ? $productsInWarehouse[$purchaseItem->product_key]->qty : '',
                    'qtyInStore' => isset($_products[$purchaseItem->account_id][$purchaseItem->product_key]) ? $_products[$purchaseItem->account_id][$purchaseItem->product_key]->qty : '',
                    'price_paid' => (isset($concatenated_costs) && trim($concatenated_costs) !== '') ? trim($concatenated_costs) : 'Por Vender',
                    'cost_before' => $purchaseItem->cost_before,
                    'cost' => $purchaseItem->cost,
                    'price_before' => $purchaseItem->price_before,
                    'price' => $purchaseItem->price,
                    'wholesale_price_before' => $purchaseItem->wholesale_price_before,
                    'wholesale_price' => $purchaseItem->wholesale_price,
                    'special_price_before' => $purchaseItem->special_price_before,
                    'special_price' => $purchaseItem->special_price,
                    'comment' => $purchaseItem->comment,
                    'reason' => isset($purchaseItem->reason)?$purchaseItem->reason:''
                ];
                fputcsv($fp, $_result, ';');
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
