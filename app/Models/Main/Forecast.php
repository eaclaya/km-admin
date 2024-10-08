<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Forecast extends ModelDBMain
{

    protected $connection = 'main';
    protected $fillable = [
        'zone',
        'seller',
        'supervisor',
        'visit_date',
        'current_date',
        'antiquity',
        'client',
        'code_number',
        'client_type',
        'product',
        'amount',
        'currency',
        'status',
        'tracing_date',
        'closing_date',
        'month',
        'comments',
        'account_id',
        'user_id',
        'client_id'
    ];
}
