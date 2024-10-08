<?php namespace App\Models\Main;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankSubaccount
 */
class BankSubaccount extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_BANK_SUBACCOUNT;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account()
    {
        return $this->belongsTo('App\Models\Main\BankAccount');
    }

}

