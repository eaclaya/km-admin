<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class CashCount	extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'id';
    protected $table = 'cash_count' ;   
	
    public function account() {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');

    }

    public function user()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    public function expenses(){
	    return $this->hasMany('App\Models\Main\Expense', 'cash_count_id', 'id');
    }

    public function incomes(){
        return $this->hasMany('App\Models\Main\MoneyIncome', 'cash_count_id', 'id');
    }

    public function payments(){
        return $this->hasMany('App\Models\Main\Payment', 'cash_count_id', 'id')->orderBy('payment_type_id', 'ASC');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Main\Invoice', 'cash_count_id', 'id');
    }

    public function credits(){
        return $this->hasMany('App\Models\Main\StoreCredit', 'cash_count_id', 'id');
    }

    public function refunds(){
        return $this->hasMany('App\Models\Main\Refund', 'cash_count_id', 'id');
    }

    public function transfers(){
        return $this->hasMany('App\Models\Main\MoneyTransfer', 'cash_count_id', 'id');
    }

    public function transfers_out($id){
        return MoneyTransfer::where('cash_count_out_id', $id )->get();
    }

    public function typePayments(){
        return PaymentType::getAll();
    }

    public function categories_income(){
        return IncomeCategory::get();
    }
}
