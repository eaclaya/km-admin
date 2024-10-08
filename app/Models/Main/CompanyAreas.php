<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAreas extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'company_areas';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];
    
    /**
     * @return mixed
     */
    public function tracesRequest()
    {
        return $this->hasMany('App\Models\Main\TracesRequest', 'company_areas_id', 'id');
    }
    public function profiles(){	
        return $this->hasMany('App\Models\Main\EmployeeProfile', 'company_areas_id', 'id');
    }
}
