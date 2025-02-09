<?php namespace App\Models\Main;

use Utils;
use DB;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vendor
 */
class PayrollPrepayment extends ModelDBMain
{

    protected $connection = 'main';

    /**
     * @var string
     */
    /**
     * @var array
     */
    protected $table = 'payroll_prepayments';
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
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

     public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee');
     }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    /**
     * @return string
     */

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return 'payroll_prepayment';
    }
}

