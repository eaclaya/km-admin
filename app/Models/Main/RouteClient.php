<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class RouteClient extends ModelDBMain
{

    protected $connection = 'main';
	
	public function route(){
		return $this->belongsTo('App\Models\Main\Route');
	}
	public function client(){
		return $this->belongsTo('App\Models\Main\Client');
	}
	
	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'client_id');
	}

	public function frequency(){
		return $this->belongsTo('App\Models\Main\Frequency', 'frequency_id');
	}

	public function visits($id){
		return Visit::where('client_id', $id)->whereDate('created_at', '=', date('Y-m-d'))->first();
	}
}
