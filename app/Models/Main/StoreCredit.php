<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class StoreCredit extends ModelDBMain
{

    protected $connection = 'main';

    protected $table = 'store_credits';

    public function getEntityType()
    {
        return ENTITY_STORE_CREDIT;
    }
    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }
    public function client(){
        return $this->belongsTo('App\Models\Main\Client');
    }
    public function employee(){
        return $this->belongsTo('App\Models\Main\Employee');
    }
    public function transactions(){
	return $this->hasMany('App\Models\Main\FinanceTransaction');
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\Main\PaymentType');
    }

    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
            ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    }
}
