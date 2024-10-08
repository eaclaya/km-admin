<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ProductRequestConfirmed extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = true;

    protected $table = 'product_requests_confirmed';

    protected $fillable = [
        'id',
        'request_confirmed_id',
        'is_enabled',
        'product_id',
        'product_key',
        'description',
        'account_id',
        'warehouse_account_id',
        'user_id',
        'qty',
        'qty_total',
        'price',
        'comments',
        'created_at'
    ];

    public function product()
    {
    	return $this->belongsTo('App\Models\Main\Product', 'product_id', 'id');
    }

    public function account(){
	return $this->belongsTo('App\Models\Main\Account', 'account_id');
    }

    public function orderRequestConfirmed()
    {
        return $this->belongsTo('App\Models\Main\OrderRequestConfirmed', 'request_confirmed_id');
    }
}
