<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Visit extends ModelDBMain
{

    protected $connection = 'main';

	public function client(){
		return $this->belongsTo('App\Models\Main\Client');
	}

	public function user(){
		return $this->belongsTo('App\Models\Main\User');
	}
}
