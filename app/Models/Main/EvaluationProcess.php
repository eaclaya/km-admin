<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationProcess extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "evaluation_process";

    protected $processTypes = [
        0 => 'process_employee',
        1 => 'process_account',
        2 => 'process_zones',
        3 => 'process_supervisors',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'evaluation_account_id',
        'evaluation_zone_id',
        'evaluation_employee_id',
        'evaluation_superviser_employee_id',
        'user_id',
        'real_user_id',
        'cycle',
        'evaluation_process_type',
        'notes',
        'percentage',
    ];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function realUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'evaluation_account_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function zone()
    {
        return $this->belongsTo('App\Models\Main\CompanyZones', 'evaluation_zone_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee', 'evaluation_employee_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function superviserEmployee()
    {
        return $this->belongsTo('App\Models\Main\SuperviserEmployee', 'evaluation_superviser_employee_id', 'id');
    }

    /**
     * @return mixed
     */
    public function items()
    {
        return $this->hasMany('App\Models\Main\ItemsEvaluationProcess', 'evaluation_process_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function getProcessType()
    {
        $type = $this->processTypes[$this->evaluation_process_type];
        return trans("texts.$type");
    }
}
