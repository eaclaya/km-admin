<?php

namespace App\Models\Main;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
class DamageProduct extends ModelDBMain
{

    protected $connection = 'main';
	use SoftDeletes;

	public function vendor(){
		return $this->belongsTo('App\Models\Main\Vendor');
	}
}
