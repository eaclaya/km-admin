<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemDiscount extends Model
{
    protected $table = "invoice_items_discount";

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo|null
    {
        return $this->belongsTo('App\Models\InvoiceDiscount', 'invoice_id', 'sync_invoices_id');
    }
}
