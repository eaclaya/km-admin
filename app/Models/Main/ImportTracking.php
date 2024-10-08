<?php
  
namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ImportTracking extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'id';
    protected $table = 'import_tracking';
	public $timestamps = false;

	public function products(){
		return $this->hasMany('App\Models\Main\ProductImportTracking', 'import_id', 'id');
	}

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Models\Main\Purchase');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Main\Vendor', 'vendor_id', 'id');
    }
}
