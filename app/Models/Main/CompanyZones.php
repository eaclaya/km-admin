<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyZones extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'company_zones';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];
    
    /**
     * @return mixed
     */
    public function accounts()
    {
        return $this->hasMany('App\Models\Main\Account', 'company_zones_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function users()
    {
        /* $users = DB::table('users')->whereNotNull('company_zones_id');
        $userSelected = array();
        foreach ($users as $user) {
            $company_zones_id = explode(",", $user->company_zones_id);
            in_array($this->id, $company_zones_id) ? $userSelected[] = $user : '';
        };
        return $userSelected; */
        
        $query = DB::table('users')->whereNotNull('company_zones_id')
        ->whereRaw('FIND_IN_SET( ? , company_zones_id )', array($this->id))
        ->get();
        return $query;
    }
}
