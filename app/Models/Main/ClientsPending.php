<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsPending extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $table = 'clients_requests';

    protected $fillable = [
        'type',
        'is_company',
        'name',
        'company_name',
        'contact_name',
        'phone',
        'seller_id',
        'route_id',
        'account_id',
        'user_id',
        'client_id',
        'latitude',
        'longitude',
        'address1',
        'frequency_id',
        'frequency_day',
        'comment',
        'state',
        'managed_by',
        'is_credit'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    public function account(){
        return $this->belongsTo('App\Models\Main\Account');	
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Main\Employee', 'seller_id');
    }

    public function route(){
		return $this->belongsTo('App\Models\Main\Route');
	}

    public function frequency()
    {
        return $this->belongsTo('App\Models\Main\Frequency');
    }
}
