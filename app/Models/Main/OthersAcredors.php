<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OthersAcredors extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    public function others_acredors_items(){	
        return $this->hasMany('App\Models\Main\OthersAcredorsItems', 'others_acredors_id')->orderBy('end_date', 'asc');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Main\Bank');
    }
}
