<?php namespace App\Models\Main;


/**
 * Class Invoice
 */
class ProductHistory extends ModelDBMain
{

    protected $connection = 'main';
    protected $dates = ['deleted_at'];

    protected $table = 'products_history';
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    protected $fillable = [
	    'account_id',
'user_id',
'created_at',
'updated_at',
'deleted_at',
'product_key',
'notes',
'cost',
'product_cost',
'qty',
'min_qty',
'public_id',
'default_tax_rate_id',
'tax_rate',
'tax_amount',
'utility_percent_price',
'utility_percent_wholesale',
'utility_percent_special',
'utility_percent_one',
'utility_percent_two',
'utility_percent_three',
'utility_amount_price',
'utility_amount_wholesale',
'utility_amount_special',
'utility_amount_one',
'utility_amount_two',
'utility_amount_three',
'price',
'wholesale_price',
'special_price',
'club_price',
'price_one',
'price_two',
'price_three',
'picture',
'brand_id',
'warehouse_id',
'warehouse_name',
'category_id',
'location',
'reason',
'related',
'vendor_id'

    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }


    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }


    
}

