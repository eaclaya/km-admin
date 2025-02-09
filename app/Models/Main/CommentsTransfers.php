<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CommentsTransfers extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'comments_transfers';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'transfers_id',
        'description',
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

    /**
     * @return mixed
     */
    public function transfer()
    {
        return $this->belongsTo('App\Models\Main\Transfer', 'transfers_id', 'id');
    }

}
