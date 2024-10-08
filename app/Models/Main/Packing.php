<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Packing extends ModelDBMain
{

    protected $connection = 'main';

    public function boxes()
    {
    	return $this->hasMany('App\Models\Main\PackingBox')->orderBy('packing_id');
    }
	
    public function packing_items(){
	    return $this->hasMany('App\Models\Main\PackingItem');
    }

    public function account()
    {
    	return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }


    public function User()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
}
