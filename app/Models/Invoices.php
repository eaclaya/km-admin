<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = "invoices";

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\InvoiceItems', 'invoice_id', 'sync_invoices_id');
    }
}
