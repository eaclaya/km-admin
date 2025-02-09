<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    protected $table = 'order_status';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'sort_order',
        'status'
    ];

    /* 
    1	Cotización
    2	Confirmación
    3	Producción
    4	Almacén China
    5	Despacho
    6	Transito
    7	Recibido */

    protected $dates = ['deleted_at'];
    public function orders(){
        return $this->hasMany('App\Models\Main\Order', 'status_id');
    }
}
