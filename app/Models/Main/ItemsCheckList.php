<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsCheckList extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'items_check_list';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'check_list_id',
        'fields_check_list_id',
        'is_check',
        'comments',
        'user_id',
        'account_id',
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
    public function checkList()
    {
        return $this->belongsTo('App\Models\Main\CheckList', 'check_list_id', 'id');
    }

    /**
     * @return mixed
     */
    public function field()
    {
        return $this->belongsTo('App\Models\Main\FieldsCheckList', 'fields_check_list_id', 'id');
    }
}
