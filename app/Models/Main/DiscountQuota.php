<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UpdatesWithEvent;

/**
 * Class Company
 */
class DiscountQuota extends ModelDBMain
{
    use SoftDeletes;
    use UpdatesWithEvent;

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
        'porcent_quotas_discount',
        'final_balance'
    ];


    public function special_negotiation()
    {
        return $this->belongsTo('App\Models\Main\SpecialNegotiation');
    }

    public function quota()
    {
        return $this->belongsTo('App\Models\Main\Quota');
    }

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

    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice');
    }

    public function getEntityType()
    {
        return ENTITY_DISCOUNT_QUOTA;
    }
}
