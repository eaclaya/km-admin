<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class DolarValue extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "dolar_value";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'currency_name',
        'active',
        'rate',
    ];

}
