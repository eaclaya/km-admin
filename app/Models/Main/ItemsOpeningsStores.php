<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsOpeningsStores extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'items_openings_stores';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'openings_stores_id',
        'fields_openings_stores_id',
        'user_id',
        'approximate_at',
        'finish_at',
        'status',
        'company_areas_id',
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
    public function field()
    {
        return $this->belongsTo('App\Models\Main\FieldsOpeningsStores', 'fields_openings_stores_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function area()
    {
        return $this->belongsTo('App\Models\Main\CompanyAreas', 'company_areas_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Main\CommentsItemsOpeningsStores', 'item_openings_stores_id', 'id');
    }
}
