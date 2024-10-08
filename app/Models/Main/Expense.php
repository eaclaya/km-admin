<?php namespace App\Models\Main;

use Utils;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasUpdated;
use App\Events\ExpenseWasDeleted;

/**
 * Class Expense
 */
class Expense extends ModelDBMain
{

    protected $connection = 'main';
    // Expenses
    use SoftDeletes;
    use PresentableTrait;
    protected $table = 'expenses';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'vendor_id',
        'expense_currency_id',
        'expense_date',
        'invoice_currency_id',
        'amount',
        'foreign_amount',
        'exchange_rate',
        'private_notes',
        'public_notes',
        'bank_id',
        'transaction_id',
	'expense_category_id',
	'expense_subcategory_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
        'adjunt',
        'real_employee_id',
        'purchase_id'
    ];

    public static function getImportColumns()
    {
        return [
            'client',
            'vendor',
            'amount',
            'public_notes',
            'expense_category',
            'expense_date',
        ];
    }

    public static function getImportMap()
    {
        return [
            'amount|total' => 'amount',
            'category' => 'expense_category',
            'client' => 'client',
            'vendor' => 'vendor',
            'notes|details' => 'public_notes',
            'date' => 'expense_date',
        ];
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
            ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    }
    
    public function expense_subcategory()
    {
        return $this->belongsTo('App\Models\Main\ExpenseSubcategory')->withTrashed();
    }

    public function expense_category()
    {
        return $this->belongsTo('App\Models\Main\ExpenseCategory')->withTrashed();
    }
	
     public function expense_items()
    {
        return $this->hasMany('App\Models\Main\ExpenseItem');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Main\Vendor')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice')->withTrashed();
    }

    public function payments()
    {
		return $this->hasMany('App\Models\Main\ExpensePayment', 'expense_id', 'id');
    }
    /**
     * @return mixed
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Main\Document')->orderBy('id');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->transaction_id) {
            return $this->transaction_id;
        } elseif ($this->public_notes) {
            return mb_strimwidth($this->public_notes, 0, 16, "...");
        } else {
            return '#' . $this->public_id;
        }
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/expenses/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_EXPENSE;
    }

    /**
     * @return bool
     */
    public function isExchanged()
    {
        return $this->invoice_currency_id != $this->expense_currency_id || $this->exchange_rate != 1;
    }

    /**
     * @return float
     */
    public function convertedAmount()
    {
        return round($this->amount * $this->exchange_rate, 2);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if(empty($this->visible) || in_array('converted_amount', $this->visible))$array['converted_amount'] = $this->convertedAmount();

        return $array;
    }

    /**
     * @param $query
     * @param null $bankdId
     * @return mixed
     */
    public function scopeBankId($query, $bankdId = null)
    {
        if ($bankdId) {
            $query->whereBankId($bankId);
        }

        return $query;
    }

    public function amountWithTax()
    {
        return Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }
}

Expense::creating(function ($expense) {
    $expense->setNullValues();
});

Expense::created(function ($expense) {
    event(new ExpenseWasCreated($expense));
});

Expense::updating(function ($expense) {
    $expense->setNullValues();
});

Expense::updated(function ($expense) {
    event(new ExpenseWasUpdated($expense));
});

Expense::deleting(function ($expense) {
    $expense->setNullValues();
});
