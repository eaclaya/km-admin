<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdatesWithEvent;

/**
 * Class Company
 */
class RefundQuota extends ModelDBMain
{
    use SoftDeletes;
    use UpdatesWithEvent;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'refund_quotas';

    protected $fillable = [
        'id',
        'special_negotiations_id',
        'quota_id',
        'account_id',
        'employee_id',
        'client_id',
        'invoice_id',
        'refund_id',
        'mount_balance',
        'mount_balance_total',
        'overdue_balance',
        'final_balance',
        'refund_at'
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

    public function getEntityType()
    {
        return ENTITY_REFUND_QUOTA;
    }
}
