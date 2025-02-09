<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class InvoiceStatus
 */
class SupplyComment extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */

	public function status(){
		return $this->belongsTo('App\Models\Main\SupplyStatus', 'supply_status_id', 'id');
	}

	public function user(){
		return $this->belongsTo('App\Models\Main\User');
	}
}

