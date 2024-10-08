<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'id';
    protected $table = 'stock_entries';

    public function product()
    {
        return $this->belongsTo('App\Models\Main\Product', 'product_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo('App\Models\Main\Stock', 'stock_id', 'id');
    }
}
