<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFile extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
      
    public $timestamps = true;
    protected $table = 'employee_files';
    /**
     * @return mixed
     */

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }


  public function employee(){	
	  return $this->belongsTo('App\Models\Main\Employee');
  }
}
