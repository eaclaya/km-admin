<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CheckList extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "check_list";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'is_verify',
        'notes',
        'is_complete',
        'comments',
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
    public function items()
    {
    	return $this->hasMany('App\Models\Main\ItemsCheckList', 'check_list_id', 'id');
    }
}
