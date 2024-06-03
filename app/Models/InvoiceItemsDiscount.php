<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemsDiscount extends Model
{
    protected $table = "invoice_items_discount";

    public function invoices(): \Illuminate\Database\Eloquent\Relations\BelongsTo|null
    {
        return $this->belongsTo('App\Models\InvoicesDiscount', 'invoice_id', 'sync_invoices_id');
    }
}
