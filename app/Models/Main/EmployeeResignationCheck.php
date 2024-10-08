<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeResignationCheck extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'employee_resignation_check';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'name',
      'description',
    ];
}
