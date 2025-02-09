<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CountTotalRelationId extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'count_total_relation_id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'relation_id',
        'qty',
    ];

    /**
     * @return mixed
     */
    public function product()
    {
        return $this->hasMany('App\Models\Main\Product', 'product_key', 'relation_id');
    }
}
