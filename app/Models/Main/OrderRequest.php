<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends ModelDBMain
{

    protected $connection = 'main';
      public $timestamps = false;

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ORDER_REQUEST;
    }

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }

  public function products(){
	return $this->hasMany('App\Models\Main\ProductRequest', 'request_id' );
  }
}
