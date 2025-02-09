<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class PaymentStatus
 */
class PaymentStatus extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;
}
