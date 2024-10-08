<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborRightsItem extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'labor_rights_items';

    public function finance_account(){
        return $this->belongsTo('App\Models\Main\FinanceAccount');
    }
}
