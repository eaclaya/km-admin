<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends ModelDBMain
{
    protected $table = "invoice_items";

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo|null
    {
        return $this->belongsTo('App\Models\Main\Invoice', 'id', 'invoice_id');
    }
}
