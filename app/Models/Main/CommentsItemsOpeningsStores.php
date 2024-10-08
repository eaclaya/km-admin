<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CommentsItemsOpeningsStores extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'comments_items_openings_stores';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'openings_stores_id',
        'item_openings_stores_id',
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
    public function openingsStores()
    {
        return $this->belongsTo('App\Models\Main\OpeningsStores', 'openings_stores_id', 'id');
    }

    /**
     * @return mixed
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Main\ItemsOpeningsStores', 'item_openings_stores_id', 'id');
    }
}
