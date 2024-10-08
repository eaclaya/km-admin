<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class TransferProductHistory extends ModelDBMain
{

    protected $connection = 'main';
	protected $primaryKey = 'id';
	protected $table = 'transfer_product_history';
	public $timestamps = false;
	
			
}
