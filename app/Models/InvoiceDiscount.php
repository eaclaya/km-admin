<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDiscount extends Model
{
    protected $table = "invoices_discount";

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\InvoiceItemDiscount', 'invoice_id', 'sync_invoice_id');
    }
}
