<?php

namespace App\Models\Main;
use \DB;

use Illuminate\Database\Eloquent\Model;

class Payment extends ModelDBMain
{
    protected $table = "payments";
    public function invoices(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Main\Invoice', 'invoice_id', 'id');
    }
    public function invoicesTotalCost()
    {
        $totalCost = DB::connection('main')->table('invoice_items')
            ->where('invoice_id', $this->invoice_id)
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->select(
                DB::raw('SUM(invoice_items.qty * products.cost) as total_cost')
            )
            ->value('total_cost');
        return isset($totalCost) ? $totalCost : 0;
    }
}
