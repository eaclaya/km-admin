<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Stock extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'id';
    protected $table = 'stock';
    public function items()
    {
        return $this->hasMany('App\Models\Main\StockEntry')->orderBy('id', 'DESC');
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
