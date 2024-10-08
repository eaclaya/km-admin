<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends BaseModel
{


    public function getEntityType()
    {
        return ENTITY_FINANCE_ACCOUNT;
    }
    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }
    public function transactions(){
	return $this->hasMany('App\Models\Main\FinanceTransaction', 'finance_account_id');
    }
}
