<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class MoneyOutcome extends ModelDBMain
{

    protected $connection = 'main';


    public function getEntityType()
    {
        return ENTITY_MONEY_OUTCOME;
    }
    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }
    public function transactions(){
	return $this->hasMany('App\Models\Main\FinanceTransaction');
    }
}
