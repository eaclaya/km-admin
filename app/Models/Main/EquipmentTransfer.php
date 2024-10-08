<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class EquipmentTransfer extends ModelDBMain
{

    protected $connection = 'main';
	public $fillable = ['from_account_id','to_account_id','user_id','product_key','qty'];

}
