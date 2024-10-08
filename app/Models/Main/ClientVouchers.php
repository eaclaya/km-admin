<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ClientVouchers extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'client_vouchers';
    /**
     * @return mixed
     */

     public function account(){
        return $this->belongsTo('App\Models\Main\Account');	
       }
    
    
      public function client(){	
          return $this->belongsTo('App\Models\Main\Client');
      }
    
     public function invoice(){
        return $this->belongsTo('App\Models\Main\Invoice');
     }
}
