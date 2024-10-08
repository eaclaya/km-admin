<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Equipment extends ModelDBMain
{

    protected $connection = 'main';
	use SoftDeletes;
	public function account(){
		return $this->belongsTo('App\Models\Main\Account');
	}

	public function employee(){
		return $this->belongsTo('App\Models\Main\Employee');
	}
}
