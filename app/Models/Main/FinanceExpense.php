<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class FinanceExpense extends BaseModel
{


    public function getEntityType()
    {
        return ENTITY_FINANCE_EXPENSE;
    }
    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }
    public function finance_account(){
        return $this->belongsTo('App\Models\Main\FinanceAccount');
    }
    public function type(){
	return $this->belongsTo('App\Models\Main\ExpenseCategory', 'expense_category_id');
    }
}
