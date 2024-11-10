<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdatesWithEvent;

/**
 * Class Company
 */
class SpecialNegotiation extends ModelDBMain
{
    use SoftDeletes;
    use UpdatesWithEvent;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'special_negotiations';

    protected $fillable = [
        'id',
        'account_id',
        'employee_id',
        'client_id',
        'route_id',
        'amount',
        'overdue_balance',
        'due_balance',
        'status',
        'is_document',
        'negotiations_discount',
        'credit_record'
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client');
    }

    public function route()
    {
        return $this->belongsTo('App\Models\Main\Route');
    }

    public function quotas()
    {
        return $this->hasMany('App\Models\Main\Quota', 'special_negotiations_id', 'id');
    }

    public function invoices()
    {
        return $this->belongsToMany('App\Models\Main\Invoice', 'negotiations_invoices', 'special_negotiations_id', 'invoice_id');
    }

    public function discounts(){
        return $this->hasMany('App\Models\Main\DiscountQuota', 'special_negotiations_id', 'id');
    }

    public function payments(){
        return $this->hasMany('App\Models\Main\PaymentQuota', 'special_negotiations_id', 'id');
    }

    public function refunds(){
        return $this->hasMany('App\Models\Main\RefundQuota', 'special_negotiations_id', 'id');
    }

    public function getEntityType()
    {
        return ENTITY_SPECIAL_NEGOTIATION;
    }
}
