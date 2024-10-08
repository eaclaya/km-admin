<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AuditsStoredFile extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
      
    public $timestamps = true;
    protected $table = 'audits_stored_files';
    /**
     * @return mixed
     */

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }


  public function AuditsStored(){	
	  return $this->belongsTo('App\Models\Main\AuditsStored');
  }
}
