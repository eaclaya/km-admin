<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount
 */
class BankAccount extends ModelDBMain
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
        return ENTITY_BANK_ACCOUNT;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo('App\Models\Main\Bank');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bank_subaccounts()
    {
        return $this->hasMany('App\Models\Main\BankSubaccount');
    }
}
