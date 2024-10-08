<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Referrer extends ModelDBMain
{

    protected $connection = 'main';
      public $timestamps = false;


    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_REFERRER;
    }

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }
}
