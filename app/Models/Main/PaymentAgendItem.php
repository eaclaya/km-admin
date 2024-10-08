<?php namespace App\Models\Main;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAgendItem extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $table = 'payment_agends_items';

    protected $fillable = [
        'user_id',
        'account_id',
        'payment_agend_id',
        'date',
        'payment',
        'sures',
        'interes',
        'capital',
        'saldo',
        'tasa',
        'tasa_anual',
        'cuota',
        'amount_delay',
        'abono',
        'comments',
        'adjunt',
    ];
    
}
