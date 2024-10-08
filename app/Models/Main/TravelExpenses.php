<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class TravelExpenses extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'travel_expenses';
    
    public function user(){
        return $this->belongsTo('App\Models\Main\User');
    }


    public function items(){
        return $this->hasMany('App\Models\Main\TravelExpensesItems');
        }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function paid_account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id_paid');
    }

    public function paid_user(){
        return $this->belongsTo('App\Models\Main\User', 'user_id_paid');
    }
}
