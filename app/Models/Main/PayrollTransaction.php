<?php namespace App\Models\Main;

use Utils;
use DB;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vendor
 */
class PayrollTransaction extends ModelDBMain
{

    protected $connection = 'main';

    /**
     * @var string
     */
    /**
     * @var array
     */
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

    public function payroll(){
	return $this->belongsTo('App\Models\Main\Payroll');
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
        return $this->hasMany('App\Models\Main\PayrollPayment');
    }

    /**
     * @return string
     */

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return 'payroll_transaction';
    }
}

