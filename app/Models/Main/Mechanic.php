<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use DB;
use DateTime;
class Mechanic extends ModelDBMain
{

    protected $connection = 'main';
    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_MECHANIC;
    }

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }


   public function invoices(){
	return $this->hasMany('App\Models\Main\Invoice', 'mechanic_id', 'id');
   }
}
