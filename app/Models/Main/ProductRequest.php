<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = false;

    public function product()
    {
    	return $this->belongsTo('App\Models\Main\Product', 'product_id', 'id');
    }

    public function account(){
	return $this->belongsTo('App\Models\Main\Account', 'account_id');
    }
}
