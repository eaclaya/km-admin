<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class CashMoney	extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'cash_money' ;   



    public function User()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
}
