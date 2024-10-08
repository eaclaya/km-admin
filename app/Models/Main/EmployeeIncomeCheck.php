<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeIncomeCheck extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'employee_income_check';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'name',
      'description',
    ];
}
