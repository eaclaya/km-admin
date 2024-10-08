<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class DailyCash	extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'cash_count' ;   


    public function cashMoney(){
	return $this->hasMany('App\Models\Main\CashMoney', 'cash_count_id', 'id');
    }

    public function User()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
}
