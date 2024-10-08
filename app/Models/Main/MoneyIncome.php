<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class MoneyIncome extends ModelDBMain
{

    protected $connection = 'main';

    protected $table = 'money_incomes';
    
    public function getEntityType()
    {
        return ENTITY_MONEY_INCOME;
    }

    public function referenceNumber()
    {
        return $this->hasOne(ReferenceNumber::class, 'table_id')->where('table_name', $this->table);
    }

    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
        ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    }
    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }
    public function transactions(){
	return $this->hasMany('App\Models\Main\FinanceTransaction');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
    public function income(){
        return $this->belongsTo('App\Models\Main\IncomeCategory', 'income_category_id', 'id');
    }
    
    public function payment_type()
    {
        return $this->belongsTo('App\Models\Main\PaymentType');
    }

    public function client()
    {
    	return $this->belongsTo('App\Models\Main\Client', 'client_id', 'id');
    }
}
