<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class CommissionRate extends ModelDBMain
{

    protected $connection = 'main';
	protected $table = 'commission_rates';

	public function employees(){
		return $this->hasMany('App\Models\Main\CommissionEmployee', 'commission_id', 'id');
	}

	public function account() {
		return $this->belongsTo('App\Models\Main\Account');
	}

}
