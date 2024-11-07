<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class TrackingHistory extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = true;
    protected $table = 'tracking_history';

    protected $fillable = [
        'model',
        'model_id',
        'before_data',
        'after_data',
        'user_id',
        'real_user_id'
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    public function currentModel($model = null){
        $model = isset($model) ? $model : $this->model;
        $thisModel = $this->snakeToCamel($model);
        $related = 'App\\Models\\Main\\'.$thisModel;
        return $this->belongsTo($related, 'model_id');
    }
    public function user(){
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
    public function realUser(){
        return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
    }

    private function snakeToCamel($string) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
