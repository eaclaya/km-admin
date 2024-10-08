<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class SuperviserEmployee extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'superviser_employee';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'employee_id',
        'company_areas_ids',
        'company_zones_ids',
        'employee_profiles_ids',
        'accounts_ids',
        'employees_ids',
        'by_areas',
        'by_profiles',
        'by_zones',
        'by_accounts',
        'by_employee'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee', 'employee_id', 'id');
    }
    public function getZones()
    {
        $allowedZonesIds = (isset($this->company_zones_ids) && $this->company_zones_ids !== null && trim($this->company_zones_ids) !== "") ? trim($this->company_zones_ids) : null;
        if ($allowedZonesIds !== null) {
            $query = DB::table('company_zones')
                ->whereRaw('FIND_IN_SET( id , ? )', array($allowedZonesIds))
                ->whereNull('deleted_at')
                ->get();
            return $query;
        }
        return [];
    }

    public function getAccountsZones()
    {
        $allowedZonesIds = (isset($this->company_zones_ids) && $this->company_zones_ids !== null && trim($this->company_zones_ids) !== "") ? explode(",", $this->company_zones_ids) : null;
        if ($allowedZonesIds !== null) {
            $accounts = Account::select(array('id'))->whereIn('company_zones_id', $allowedZonesIds)->get()->keyBy('id')->keys()->toArray();
            return $accounts;
        } else {
            return [];
        }
    }

    public function getAreas()
    {
        $allowedAreasIds = (isset($this->company_areas_ids) && $this->company_areas_ids !== null && trim($this->company_areas_ids) !== "") ? trim($this->company_areas_ids) : null;
        if ($allowedAreasIds !== null) {
            $query = DB::table('company_areas')
                ->select('id','name')
                ->whereRaw('FIND_IN_SET( id , ? )', array($allowedAreasIds))
                ->whereNull('deleted_at')
                ->get();
            return $query;
        } else {
            return [];
        }
    }
    
    public function getProfileAreas()
    {
        $allowedAreasIds = (isset($this->company_areas_ids) && $this->company_areas_ids !== null && trim($this->company_areas_ids) !== "") ? trim($this->company_areas_ids) : null;
        if ($allowedAreasIds !== null) {
            $query = DB::table('employee_profile')
                ->select('id','name')
                ->whereRaw('FIND_IN_SET( company_areas_id , ? )', array($allowedAreasIds))
                ->whereNull('deleted_at')
                ->get();
            return $query;
        } else {
            return [];
        }
    }
    
    public function getProfiles()
    {
        $allowedProfilesIds = (isset($this->employee_profiles_ids) && $this->employee_profiles_ids !== null && trim($this->employee_profiles_ids) !== "") ? trim($this->employee_profiles_ids) : null;
        if ($allowedProfilesIds !== null) {
            $query = DB::table('employee_profile')
                ->select('id','name')
                ->whereRaw('FIND_IN_SET( id , ? )', array($allowedProfilesIds))
                ->whereNull('deleted_at')
                ->get();
            return $query;
        }else{
            return [];
        }
    }
    
    public function getAccounts()
    {
        $allowedAccountsIds = (isset($this->accounts_ids) && $this->accounts_ids !== null && trim($this->accounts_ids) !== "") ? trim($this->accounts_ids) : null;
        if ($allowedAccountsIds !== null) {
            $query = DB::table('accounts')
                ->select('id','name')
                ->whereRaw('FIND_IN_SET( id , ? )', array($allowedAccountsIds))
                ->whereNull('deleted_at')
                ->get();
            return $query;
        }else{
            return [];
        }
    }

    public function getEmployees()
    {
        $allowedEmployeesIds = (isset($this->employees_ids) && $this->employees_ids !== null && trim($this->employees_ids) !== "") ? trim($this->employees_ids) : null;
        if ($allowedEmployeesIds !== null) {
            $query = DB::table('employees')
                ->select('id','first_name','last_name')
                ->whereRaw('FIND_IN_SET( id , ? )', array($allowedEmployeesIds))
                ->get();
            return $query;
        }else{
            return [];
        }
    }
    
    public function getFilters()
    {
        return [
            'by_areas' => $this->by_areas,
            'by_profiles' => $this->by_profiles,
            'by_zones' => $this->by_zones,
            'by_accounts' => $this->by_accounts,
            'by_employee' => $this->by_employee
        ];
    }

    public function getAllData(){
        return [
            'superviser_id'=> $this->id,
            'employee' => $this->employee,
            'zones' => $this->getZones(),
            'areas' => $this->getAreas(),
            'profiles' => $this->getProfiles(),
            'accounts' => $this->getAccounts(),
            'employees' => $this->getEmployees(),
            'filters' => $this->getFilters()
        ];
    }
}
