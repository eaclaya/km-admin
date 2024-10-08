<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class PaymentLibrary
 */
class PaymentLibrary extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var string
     */
    protected $table = 'payment_libraries';
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gateways()
    {
        return $this->hasMany('App\Models\Main\Gateway', 'payment_library_id');
    }
}
