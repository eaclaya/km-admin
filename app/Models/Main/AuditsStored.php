<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AuditsStored extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "audits_stored";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'last_at',
        'next_at',
        'type',
        'notes',
        'missing',
        'surplus',
        'file',
        'finish_at',
        'counted_codes',
        'estimated_codes',
        'counted_pieces',
        'estimated_pieces',
        'sample',
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
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }
    
    public function files(){
        return $this->hasMany('App\Models\Main\AuditsStoredFile', 'audits_stored_id', 'id');
    }
}
