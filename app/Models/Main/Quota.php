<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class Quota extends ModelDBMain
{
    use SoftDeletes;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'quotas';

    protected $fillable = [
        'id',
        'special_negotiations_id',
        'account_id',
        'employee_id',
        'client_id',
        'days_exceeded',
        'initial_balance',
        'monthly_payment',
        'status',
        'credit_start_at',
        'credit_payment_at',
        'discount_applied'
    ];


    public function special_negotiations()
    {
        return $this->belongsTo('App\Models\Main\SpecialNegotiation');
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
        return $this->belongsToMany('App\Models\Main\Invoice', 'quotas_invoices', 'quota_id', 'invoice_id');
    }
}
