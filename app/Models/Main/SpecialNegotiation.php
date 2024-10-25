<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class SpecialNegotiation extends ModelDBMain
{
    use SoftDeletes;

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
        'negotiations_discount'
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

    public function invoices()
    {
        return $this->belongsToMany('App\Models\Main\Invoice', 'negotiations_invoices', 'special_negotiations_id', 'invoice_id');
    }
}
