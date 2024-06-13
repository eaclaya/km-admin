<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = "invoices";

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\InvoiceItem', 'invoice_id', 'sync_invoice_id');
    }
}
