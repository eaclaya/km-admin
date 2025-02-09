<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsEvaluationProcess extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "items_evaluation_process";

    protected $processType = [
        1 => 'employee',
        2 => 'account',
        3 => 'zone',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'evaluation_process_id',
        'user_id',
        'real_user_id',
        'evaluation_account_id',
        'evaluation_zone_id',
        'evaluation_employee_id',
        'evaluation_superviser_employee_id',
        'evaluation_process_type',
        'fields_evaluation_process_id',
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
    public function field()
    {
        return $this->belongsTo('App\Models\Main\FieldsEvaluationProcess', 'fields_evaluation_process_id', 'id');
    }
    
    public function evaluationProcess()
    {
        return $this->belongsTo('App\Models\Main\EvaluationProcess', 'fields_evaluation_process_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function getProcessType()
    {
        return $this->processType[$this->evaluation_process_type];
    }
}
