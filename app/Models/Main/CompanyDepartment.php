<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDepartment extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'company_department';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];
    
    public function areas(){	
        return $this->hasMany('App\Models\Main\CompanyAreas', 'company_department_id', 'id');
    }
    public function employees(){	
        return $this->hasMany('App\Models\Main\Employee', 'company_department_id', 'id');
    }
}
