<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'orders';
    public $timestamps = true;
    protected $casts = [
        'orders_related' => 'array'
    ];
    protected $fillable = [
        'id',
        'user_id',
        'sort_order',
        'status_id',
        'is_complete',
        'order_number',
        'order_date',
        'quantity',
        'amount',
        'files',
        'emergency',
        'departure_date',
        'arrival_date',
    ];

    protected $dates = ['deleted_at'];
    public function items(){
        return $this->hasMany('App\Models\Main\OrderItem', 'order_id');
    }
    
    public function status(){
        return $this->belongsTo('App\Models\Main\OrderStatus');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id');
    }
    
}