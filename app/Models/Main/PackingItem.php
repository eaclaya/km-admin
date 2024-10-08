<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class PackingItem extends ModelDBMain
{

    protected $connection = 'main';
    
    public function packing(){
	    return $this->belongsTo('App\Models\Main\Packing', 'packing_id', 'id');
    }

    public function account(){
	    return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }

    public function product()
    {
    	return $this->belongsTo('App\Models\Main\Product', 'product_id', 'id');
    }
}
