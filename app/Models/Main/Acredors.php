<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acredors extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    public function acredors_items(){	
        return $this->hasMany('App\Models\Main\AcredorsItems', 'acredor_id');
    }

    public function last_acredors_items($acredor_id){	
        return AcredorsItems::where('acredor_id', $acredor_id)->orderBy('id', 'desc')->first();
    }
     

}
