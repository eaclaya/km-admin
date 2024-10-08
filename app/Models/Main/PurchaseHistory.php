<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
	protected $table = 'purchases_history';

    /**
     * @return mixed
     */
    
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function expenses(){
        return $this->hasMany('App\Models\Main\Expense')->orderBy('id');
    }
}
