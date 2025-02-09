<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class License
 */
class License extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = true;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
}
