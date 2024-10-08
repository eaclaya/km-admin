<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Message extends ModelDBMain
{

    protected $connection = 'main';

	public function chat(){
		return $this->belongsTo('App\Models\Main\Chat');
	}	
}
