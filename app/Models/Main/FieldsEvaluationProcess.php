<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FieldsEvaluationProcess extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = "fields_evaluation_process";

    protected $processType = [
        1 => 'employee',
        2 => 'account',
        3 => 'zone',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'concept',
        'sub_concept_id',
        'percentage_limit',
        'evaluator_area_id',
        'user_id',
        'real_user_id',
        'evaluation_process_type',
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
    public function area()
    {
        return $this->belongsTo('App\Models\Main\CompanyAreas', 'evaluator_area_id', 'id');
    }

    /**
     * @return mixed
     */
    public function items()
    {
        return $this->hasMany('App\Models\Main\ItemsEvaluationProcess', 'fields_evaluation_process_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function subFields()
    {
        return $this->hasMany('App\Models\Main\FieldsEvaluationProcess', 'sub_concept_id', 'id');
    }

    /**
     * @return mixed
     */
    public function getProcessType()
    {
        return $this->processType[$this->evaluation_process_type];
    }
}
