<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class StockTransactionProduct extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'id';
    protected $table = 'stock_transaction_products';


    public function stock()
    {
        return $this->belongsTo('App\Models\Main\Stock', 'stock_id', 'id');
    }
}
