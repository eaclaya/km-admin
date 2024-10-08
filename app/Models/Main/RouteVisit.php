<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class RouteVisit extends ModelDBMain
{

    protected $connection = 'main';

	public function route(){
		return $this->belongsTo('App\Models\Main\Route');
	}
	public function user(){
		return $this->belongsTo('App\Models\Main\Route');
	}
}
