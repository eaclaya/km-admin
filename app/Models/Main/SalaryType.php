<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class SalaryType extends ModelDBMain
{

    protected $connection = 'main';
      public $timestamps = false;


    /**
     * @return mixed
     */

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }
}
