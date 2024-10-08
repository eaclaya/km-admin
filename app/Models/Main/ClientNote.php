<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ClientNote extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'client_notes';
    /**
     * @return mixed
     */

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }


  public function client(){	
	  return $this->belongsTo('App\Models\Main\Client');
  }

}
