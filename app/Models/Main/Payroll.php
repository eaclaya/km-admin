<?php namespace App\Models\Main;

use Utils;
use DB;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vendor
 */
class Payroll extends ModelDBMain
{

    protected $connection = 'main';

    /**
     * @var string
     */
    /**
     * @var array
     */
    protected $table = 'payroll';
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }


    public function employee()
    {
	return $this->belongsTo('App\Models\Main\Employee');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('App\Models\Main\PayrollPayment')->orderBy('id', 'DESC');
    }

     public function prepayments()
    {
        return $this->hasMany('App\Models\Main\PayrollPrepayment')->orderBy('id', 'DESC');
    }

     public function transactions()
    {
        return $this->hasMany('App\Models\Main\PayrollTransaction')->orderBy('id', 'DESC');
    }
    /**
     * @return string
     */

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return 'payroll_balance';
    }

    /* public function get_amount_bruto(){
        $payroll->amount + $payroll->deductions + $payroll->advance + $payroll->loan;
    } */
}

