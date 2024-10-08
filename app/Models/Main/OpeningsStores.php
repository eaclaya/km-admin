<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class OpeningsStores extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "openings_stores";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'approximate_at',
        'finish_at',
        'notes',
        'status',
    ];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function items()
    {
        return $this->hasMany('App\Models\Main\ItemsOpeningsStores', 'openings_stores_id', 'id');
    }
}
