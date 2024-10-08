<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CountTotalProductKey extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'count_total_product_key';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'product_key',
        'qty',
    ];

    /**
     * @return mixed
     */
    public function product()
    {
        return $this->hasMany('App\Models\Main\Product', 'product_key', 'product_key');
    }
}
