<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicesDiscount extends Model
{
    protected $table = "invoices_discount";

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\InvoiceItemsDiscount', 'invoice_id', 'sync_invoices_id');
    }
}
