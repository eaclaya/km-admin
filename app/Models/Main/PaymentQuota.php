<?php

namespace App\Models\Main;

use App\Traits\UpdatesWithEvent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class PaymentQuota extends ModelDBMain
{
    use SoftDeletes;
    use UpdatesWithEvent;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'payment_quotas';

    protected $fillable = [
        'special_negotiations_id',
        'quota_id',
        'account_id',
        'employee_id',
        'client_id',
        'invoice_id',
        'payment_id',
        'mount_balance',
        'mount_balance_total',
        'overdue_balance',
        'final_balance',
        'payment_at',
    ];

    public function special_negotiations()
    {
        return $this->belongsTo('App\Models\Main\SpecialNegotiation');
    }

    public function quota()
    {
        return $this->belongsTo('App\Models\Main\Quota');
    }

    public function accounts()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee');
    }

    public function clients()
    {
        return $this->belongsTo('App\Models\Main\Client');
    }

    public function invoices()
    {
        return $this->belongsTo('App\Models\Main\Invoice');
    }

    public function getEntityType()
    {
        return ENTITY_PAYMENT_QUOTA;
    }
}
