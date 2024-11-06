<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class DiscountQuota extends ModelDBMain
{
    use SoftDeletes;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'discount_quotas';

    protected $fillable = [
        'id',
        'special_negotiations_id',
        'quota_id',
        'account_id',
        'employee_id',
        'client_id',
        'invoice_id',
        'discount_applied',
        'porcent_quotas_discount'
    ];


    public function special_negotiations()
    {
        return $this->hasMany('App\Models\Main\SpecialNegotiation');
    }

    public function quota()
    {
        return $this->hasMany('App\Models\Main\Quota');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Main\Account');
    }

    public function employee()
    {
        return $this->hasMany('App\Models\Main\Employee');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Main\Client');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice');
    }
}
