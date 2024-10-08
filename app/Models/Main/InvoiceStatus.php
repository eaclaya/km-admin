<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class InvoiceStatus
 */
class InvoiceStatus extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;
}
