<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeProfile extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'employee_profile';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'name',
      'description',
      'company_areas_id',
    ];

    public function employees(){
      return $this->hasMany('App\Models\Main\Employee', 'employee_profile_id', 'id');
    }

    public function task(){	
      return $this->hasMany('App\Models\Main\TaskKpis', 'profile_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Main\CompanyAreas', 'company_areas_id', 'id');
    }
}
