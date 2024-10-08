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

class ReportStockByVendor extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $chunk, $nameFile, $group, $_vendors, $_accounts, $is_root, $reportProcessId, $vendor;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chunk, $nameFile, $group, $_vendors, $_accounts, $is_root, $reportProcessId, $vendor)
    {
        $this->chunk = $chunk;
        $this->nameFile = $nameFile;
        $this->group = $group;
        $this->_vendors = $_vendors;
        $this->_accounts = $_accounts;
        $this->is_root = $is_root;
        $this->reportProcessId = $reportProcessId;
        $this->vendor = $vendor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chunk = $this->chunk;
        $nameFile = $this->nameFile;
        $group = $this->group;
        $_vendors = $this->_vendors;
        $_accounts = $this->_accounts;
        $is_root = $this->is_root;
        $reportProcessId = $this->reportProcessId;
        $vendor = $this->vendor;

        $exception = null;
        try {
            $products = Product::with('account')
            ->select('account_id', 'updated_at', 'product_key', 'notes', 'cost', 'qty', 'relation_id', 'price', 'wholesale_price', 'special_price', 'vendor_id')
            ->orderBy( DB::raw("CONVERT('product_key', CHAR)") )
            ->where('vendor_id', $vendor)
            ->skip(intval($chunk['skip']))->take(intval($chunk['take']))
            ->get();

            $to_date = Carbon::now();
            $from_date = $to_date->copy()->subMonths(3)->toDateTimeString();

            $to_date = $to_date->toDateTimeString();

            $productsKeys = array_keys($products->keyBy('product_key')->toArray());
            $relationsIds = array_keys($products->keyBy('relation_id')->toArray());

            $_products = CountTotalProductKey::select('product_key',DB::raw('qty as qty_total'))
                ->whereIn('product_key', $productsKeys)
                ->get()->keyBy('product_key');

            $relatedProducts = CountTotalRelationId::select('relation_id', 'qty')
                ->whereIn('relation_id', $relationsIds)
                ->get()->keyBy('relation_id');

            $accountExclude = Account::select('id', 'name')
                ->where('exclude', '>', 0)
                ->get()->keyBy('id')->toArray();
            $accountExclude = array_keys($accountExclude);

            $relationIdsKeys = array_keys($relatedProducts->toArray());

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
            $result = [];

            foreach($products as $product){
                if($product->account->exclude){ continue; }
                if($group){
                    if(isset($result[$product->product_key]) == false){
                        $result[$product->product_key] = [
                            'product_key' => $product->product_key,
                            'notes' => $product->notes,
                            'vendor' => isset($_vendors[$product->vendor_id]) ? $_vendors[$product->vendor_id]['name'] : '',
                            'cost' => $product->cost,
                            'price' => $product->price,
                            'wholesale_price' => $product->wholesale_price,
                            'special_price' => $product->special_price,
                            'qty' => 0,
                            'qty_global' => isset($_products[$product->product_key]) ? $_products[$product->product_key]->qty_total : 0,
                            'accounts' => [],
                            'updated_at' => '',
                            'relation_id' => $product->relation_id,
                            'relation_qty_global' => isset($relatedProducts[$product->relation_id]) ? $relatedProducts[$product->relation_id]->qty : 0,
                            'relation_sales_global' => isset($sales[$product->relation_id]) ? $sales[$product->relation_id]->total : 0,
                        ];
                    }
                    $result[$product->product_key]['qty'] += intval($product->qty);
                    $result[$product->product_key]['accounts'][$product->account_id] = $_accounts[$product->account_id]['name'];
                }else{
                    $result[] = [
                        'product_key' => $product->product_key,
                        'notes' => $product->notes,
                        'vendor' => isset($_vendors[$product->vendor_id]) ? $_vendors[$product->vendor_id]['name'] : '',
                        'cost' => $product->cost,
                        'price' => $product->price,
                        'wholesale_price' => $product->wholesale_price,
                        'special_price' => $product->special_price,
                        'qty' => $product->qty,
                        'qty_global' => isset($_products[$product->product_key]) ? $_products[$product->product_key]->qty_total : 0,
                        'accounts' => [$_accounts[$product->account_id]['name']],
                        'updated_at' => $product->updated_at ? date('Y-m-d H:i:s', strtotime($product->updated_at)) : '',
                        'relation_id' => $product->relation_id,
                        'relation_qty_global' => isset($relatedProducts[$product->relation_id]) ? $relatedProducts[$product->relation_id]->qty : 0,
                        'relation_sales_global' => isset($sales[$product->relation_id]) ? $sales[$product->relation_id]->total : 0,
                    ];
                }
            }

            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');
            $bom = "\xEF\xBB\xBF";
            fwrite($fp, $bom);
            foreach($result as $item){
                if(!$is_root){
                    unset($item['cost']);
                }
                $item['accounts'] = implode(',', $item['accounts']);
                fputcsv($fp, $item, ';');
            }
            fclose($fp);
        }catch (\Exception $e){
            $exception = $e;
            dump($e);
        }

        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : $reportProcess->count_rows + 1;
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
