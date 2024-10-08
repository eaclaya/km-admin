<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class EmployeeHour extends ModelDBMain
{

    protected $connection = 'main';
    /**
     * @return mixed
     */

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }


  public function employee(){	
	  return $this->belongsTo('App\Models\Main\Employee');
  }

   public function payroll(){
          return $this->belongsTo('App\Models\Main\Payroll');
  }
}
