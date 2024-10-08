<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class LaborRightsXivItem extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'labor_rights_xiv_items';

    public function finance_account(){
        return $this->belongsTo('App\Models\Main\FinanceAccount');
    }
}
