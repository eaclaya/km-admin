<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class Currency
 */
class Currency extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'swap_currency_symbol' => 'boolean',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
