<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OthersAcredorsItems extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    public function others_acredors_payments(){	
        return $this->hasMany('App\Models\Main\OthersAcredorsPayments', 'others_acredors_items_id');
    }
}
