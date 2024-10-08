<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ClientsBlockedHistory extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'clients_blocked_history';

    protected $fillable = [
        'client_id',
        'is_blocked',
        'blocked_at',
        'blocked_by',
        'balance',
        'limit_credit',
        'unlocked_at',
        'unlocked_by',
        'payment_promise',
        'comments_promise',
        'payment_id',
        'money_income_id'
    ];

    public function client(){	
        return $this->belongsTo('App\Models\Main\Client');
    }
}
