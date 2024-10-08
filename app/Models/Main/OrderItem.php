<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'order_items';
    public $timestamps = true;
    //protected $fillable = ['name','sort_order','status','created_at','updated_at', 'deleted_at'];
    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'product_id',
        'product_key',
        'description',
        'cost',
        'qty',
        'original_qty',
        'picture',
        'notes',
        'comments',
        'original_cost',
        'model',
        'translation',
        'cbm',
        'g_w'
    ];
    
    protected $dates = ['deleted_at'];
    
    public function user()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id'); //, 'id'
    }

    public function order(){
        return $this->belongsTo('App\Models\Main\Order', 'order_id');
    }

    public function product()
    {
    	return $this->belongsTo('App\Models\Main\Product', 'product_key', 'product_key'); //, 'id'
    }
}
