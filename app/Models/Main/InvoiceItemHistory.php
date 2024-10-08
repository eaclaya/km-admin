<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InvoiceItem
 */
class InvoiceItemHistory extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $table = 'invoice_items_history';
    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_INVOICE_ITEM;
    }

    /**
     * @var array
     */
    protected $fillable = [
	    "account_id",
"user_id",
"invoice_id",
"product_id",
"created_at",
"updated_at",
"deleted_at",
"product_key",
"notes",
"cost",
"qty",
"tax_name1",
"tax_rate1",
"public_id",
"custom_value1",
"custom_value2",
"tax_name2",
"tax_rate2"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\InvoiceHistory');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Main\Product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

}
