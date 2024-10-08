<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ReportsHistory extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = true;
    protected $table = 'reports_history';

    protected $fillable = [
        'user_id',
        'real_user_id',
        'url',
        'playload'

    ];

    protected $casts = [
        'playload' => 'array'
    ];

    public function user(){
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    public function realUser(){
        return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
    }
}
