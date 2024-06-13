<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = "invoice_items";

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo|null
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice_id', 'sync_invoice_id');
    }
}
