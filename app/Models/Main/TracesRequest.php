<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TracesRequest extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = "traces_request";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'is_verify',
        'notes',
        'is_complete',
        'comments',
        'company_areas_id',
    ];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function area()
    {
        return $this->belongsTo('App\Models\Main\CompanyAreas', 'company_areas_id', 'id');
    }

    /**
     * @return mixed
     */
    public function items()
    {
    	return $this->hasMany('App\Models\Main\ItemsTracesRequest', 'traces_request_id', 'id');
    }
    
}
