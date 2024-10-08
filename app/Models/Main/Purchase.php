<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Purchase extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public function items()
    {
        return $this->hasMany('App\Models\Main\PurchaseItem')->orderBy('id');
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PURCHASE;
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Main\Vendor', 'vendor_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Main\Vendor');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Main\Expense')->orderBy('id');
    }

    public function purchase_history()
    {
        return $this->belongsTo('App\Models\Main\PurchaseHistory', 'purchase_id', 'id');
    }
}
