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
        'invoice_id',
        'amount',
        'overdue_balance',
        'due_balance',
        'status',
        'is_document',
        'negotiations_discount'
    ];

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
