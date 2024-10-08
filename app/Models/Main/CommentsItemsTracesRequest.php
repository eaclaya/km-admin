<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CommentsItemsTracesRequest extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'comments_items_traces_request';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'traces_request_id',
        'item_traces_request_id',
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
    public function tracesRequest()
    {
        return $this->belongsTo('App\Models\Main\TracesRequest', 'traces_request_id', 'id');
    }

    /**
     * @return mixed
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Main\ItemsTracesRequest', 'item_traces_request_id', 'id');
    }
}
