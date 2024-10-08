<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends ModelDBMain
{

    protected $connection = 'main';


    public function getEntityType()
    {
        return ENTITY_FINANCE_TRANSACTION;
    }
    public function account(){
	return $this->belongsTo('App\Models\Main\Account');
    }
    public function finance_account(){
        return $this->belongsTo('App\Models\Main\FinanceAccount');
    }
    public function finance_type(){
	return $this->belongsTo('App\Models\Main\FinanceType', 'finance_type_id');
    }
    public function transaction_type(){
        return $this->belongsTo('App\Models\Main\TransactionType', 'transaction_type_id');
    }
    public function user(){
	return $this->belongsTo('App\Models\Main\User');
    }
}
