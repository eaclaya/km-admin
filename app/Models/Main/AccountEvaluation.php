<?php namespace App\Models\Main;

use Utils;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vendor
 */
class AccountEvaluation extends ModelDBMain
{

    protected $connection = 'main';

    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }



    /**
     * @return string
     */

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return 'account_evaluation';
    }
}
