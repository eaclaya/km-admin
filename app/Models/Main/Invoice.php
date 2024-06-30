<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Invoice extends ModelDBMain
{
    protected $table = "invoices";

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\Main\InvoiceItem', 'invoice_id', 'id');
    }
    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\Main\Payment', 'invoice_id', 'id');
    }
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }
    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Main\Client', 'client_id', 'id');
    }
}
