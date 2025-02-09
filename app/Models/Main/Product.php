<?php namespace App\Models\Main;


use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Main\InvoiceItem;
use Auth;
use App\Models\Main\StockEntry;
/**
 * Class Product
 */
class Product extends ModelDBMain
{

    protected $connection = 'main';

    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';

    /**
     * @var array
     */
    protected $fillable = [
        'product_key',
        'notes',
        'category_id',
        'cost',
        'qty',
        'min_qty',
        'default_tax_rate_id',
        'price',
        'wholesale_price',
        'special_price',
        'location',
        'brand_id'
    ];

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            'id',
            'public_id',
            'product_key',
            'notes',
            'category_id',
            'sub_category_id',
            'rotation_id',
            'cost',
            'price',
            'qty',
            'min_qty',
            'max_qty',
            'wholesale_price',
            'special_price',
            'price_two',
            'price_three',
            'warehouse_name',
            'location',
            'vendor_id',
            'relation_id',
            'invoice_date',
            'brand_id'
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'product|item' => 'product_key',
            'notes|description|details' => 'notes',
            'cost|amount|price' => 'cost',
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return Product::scope()->where('product_key', '=', "$key")->first();
    }
    public function account(){
	return $this->belongsTo('App\Models\Main\Account');
    }
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Main\Brand', 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Main\Category', 'category_id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Main\Vendor', 'vendor_id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Main\Warehouse', 'warehouse_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_tax_rate()
    {
        return $this->belongsTo('App\Models\Main\TaxRate');
    }

     public function lastInvoice(){
        $items = InvoiceItem::where('account_id', Auth::user()->account_id)->where('product_key', $this->product_key)->orderBy('invoice_id','DESC')->get();
        foreach($items as $item){
                if($item->invoice && $item->invoice->invoice_type_id == 1){
                        return $item->invoice->invoice_date;
                }
        }
        return 'N/A';
     }
    public function lastStock(){
	$entry = StockEntry::where('account_id', Auth::user()->account_id)->where('product_key', $this->product_key)->orderBy('created_at', 'DESC')->first();
                    if($entry)
                    {
                        return $entry->created_at->format('Y-m-d');
                    }
                    return 'N/A';
    }

    public function quantityHistory(){
	$entry = StockEntry::where('account_id', Auth::user()->account_id)->where('product_key', $this->product_key)->orderBy('created_at', 'DESC')->first();
                    if($entry)
                    {
                        return $entry->qty_history;
                    }
                    return 'N/A';
    }
}
