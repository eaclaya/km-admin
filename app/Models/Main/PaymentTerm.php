<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm
 */
class PaymentTerm extends ModelDBMain
{

    protected $connection = 'main';
    //use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PAYMENT_TERM;
    }
    
}
