<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class PackingBox extends ModelDBMain
{

    protected $connection = 'main';

    public function items()
    {
    	return $this->hasMany('App\Models\Main\PackingItem', 'packing_box', 'id')->orderBy('notes', 'ASC');
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
