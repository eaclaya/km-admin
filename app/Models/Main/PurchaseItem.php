<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends ModelDBMain
{

    protected $connection = 'main';

    protected $primaryKey = 'id';
    
    
    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PURCHASE_ITEM;
    }

    public function purchase() {
        return $this->belongsTo('App\Models\Main\Purchase');
    }

}
