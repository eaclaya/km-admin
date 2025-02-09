<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class OrderRequestConfirmed extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = true;

    protected $table = 'order_requests_confirmed';

    protected $fillable = [
        'id',
        'order_request_id',
        'invoice_id',
        'confirmed_key',
        'account_id',
        'comments',
        'client_id',
        'status',
        'warehouse_account_id',
        'items_count',
        'grand_total',
        'audit_account_id',
        'audit_user_id',
        'sheet_id',
        'created_at'
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ORDER_REQUEST_CONFIRMED;
    }

    public function getStatus()
    {
        if ($this->status == 1) {
            return 'en_verificacion';
        }
        if ($this->status == 2) {
            return 'packing';
        }
        if ($this->status == 3) {
            return 'transferencia';
        }
        if ($this->status == 4) {
            return 'facturado';
        }
        if ($this->status == 5) {
            return 'completado';
        }
        if ($this->status == 6) {
            return 'negado';
        }
    }

    public function account(){
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function client(){
        return $this->belongsTo('App\Models\Main\Client', 'client_id' )->withTrashed();
    }

    public function invoice(){
        return $this->belongsTo('App\Models\Main\Invoice', 'invoice_id' );
    }

    public function products(){
        return $this->hasMany('App\Models\Main\ProductRequestConfirmed', 'request_confirmed_id' );
    }
}
