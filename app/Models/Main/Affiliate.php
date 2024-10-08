<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class Affiliate
 */
class Affiliate extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var bool
     */
    protected $softDelete = true;
}
