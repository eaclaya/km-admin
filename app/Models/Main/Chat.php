<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Chat extends ModelDBMain
{

    protected $connection = 'main';

	public function messages(){
		return $this->hasMany('App\Models\Main\Message');
	}	
}
