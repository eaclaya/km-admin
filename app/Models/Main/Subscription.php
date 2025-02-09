<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent;

/**
 * Class Subscription
 */
class Subscription extends ModelDBMain
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
