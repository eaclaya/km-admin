<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Relation extends ModelDBMain
{

    protected $connection = 'main';
	public $incrementing = false;
	public function products(){
		return $this->hasMany('App\Models\Main\Product', 'relation_id', 'id');
	}
}
