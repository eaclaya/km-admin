<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Payment extends ModelDBMain
{
    protected $table = "payments";
    public function invoices(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Main\Invoice', 'invoice_id', 'id');
    }
}
