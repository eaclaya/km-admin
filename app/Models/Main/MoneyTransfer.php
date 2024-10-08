<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'money_transfers';

    public function referenceNumber()
    {
        return $this->hasOne(ReferenceNumber::class, 'table_id')->where('table_name', $this->table);
    }

    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
        ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    }
    
    public function getEntityType()
    {
        return ENTITY_MONEY_TRANSFER;
    }
    public function from_account(){
        return $this->belongsTo('App\Models\Main\Account', 'from_account_id');
    }
    public function to_account(){
	return $this->belongsTo('App\Models\Main\Account', 'to_account_id');
    }
    public function from_finance(){
	return $this->belongsTo('App\Models\Main\FinanceAccount', 'from_finance_id');
    }
    public function to_finance(){
	return $this->belongsTo('App\Models\Main\FinanceAccount', 'to_finance_id');
    }
    public function transactions(){
	return $this->hasMany('App\Models\Main\FinanceTransaction');
    }
}
