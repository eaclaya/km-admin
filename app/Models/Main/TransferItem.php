<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TransferItem extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $primaryKey = 'id';
    
    public $timestamps = false;

    public function product()
    {
    	return $this->belongsTo('App\Models\Main\Product', 'product_id', 'id');
    }
    public function transfer(){
	return $this->belongsTo('App\Models\Main\Transfer', 'transfer_id', 'id');
    }
}	
