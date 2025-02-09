<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Referral extends ModelDBMain
{

    protected $connection = 'main';
      public $timestamps = false;


    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_REFERRAL;
    }

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }
}
