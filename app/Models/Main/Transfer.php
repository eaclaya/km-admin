<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transfer extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $primaryKey = 'id';    
    public $timestamps = false;

    public function items()
    {
    	return $this->hasMany('App\Models\Main\TransferItem')->orderBy('transfer_id');
    }
    
    public function comments()
    {
    	return $this->hasMany('App\Models\Main\CommentsTransfers', 'transfers_id', 'id');
    }

    public function fromAccount()
    {
    	return $this->belongsTo('App\Models\Main\Account', 'from_account_id', 'id');
    }

    public function toAccount()
    {
    	return $this->belongsTo('App\Models\Main\Account', 'to_account_id', 'id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }
    public function packing()
    {
    	return $this->belongsTo('App\Models\Main\Packing', 'id', 'transfer_id');
    }
}
