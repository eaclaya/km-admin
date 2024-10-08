<?php

namespace App\Models\Main;

use Session;
use Auth;
use Event;
use Cache;
use DB;
use App\Libraries\Utils;
use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 */
class User extends Authenticatable
{
    /**
     * @var array
     */
    public static $all_permissions = [
        'create_all' => 0b0001,
        'view_all' => 0b0010,
        'edit_all' => 0b0100,
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'disabled',
        'company_zones_id'
    ];

    public $owners = [1, 7, 9, 10, 16, 20, 22, 24, 27, 29, 38, 40, 46, 47, 49, 50, 52, 54, 56, 60, 63, 65, 66, 70, 73, 78, 79, 80, 86, 93, 98, 99, 100, 104, 105, 125];

    public $superusers = [1, 9, 10, 16, 22, 27, 38, 47, 50, 52, 54, 63, 65, 66, 78, 79, 86, 92, 93, 95, 96, 97, 98, 99, 100, 104, 105, 125, 127, 128, 129];

    // public $superadmins = [1, 9, 10, 16, 22, 27, 38, 47, 50, 52, 54, 63, 65, 66, 78, 95, 104, 105, 134];

    public $root = [16, 50, 52, 63];

    public $accounting = [1, 9, 16, 50, 52, 63];

    public $accounts = [16, 47, 52, 62, 63, 78];

    public $packings = [1, 16, 22, 38, 52, 54, 84, 85, 86, 91, 99, 100, 160];

//    public $inventory = [1, 10, 16, 22, 38, 47, 50, 52, 54, 63, 65, 78, 86, 99, 100, 134, 144, 47, 104, 105, 121, 140, 141];

    public $hhrr = [9, 16, 50, 52, 62, 63, 78];

    public $isolated = [];
    public $double_points = [];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'confirmation_code'];

    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getEntityType()
    {
        return ENTITY_USER;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Main\UserRole');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo('App\Models\Main\Theme');
    }

    /**
     * @param $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $this->attributes['username'] = $value;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getDisplayName();
    }

    /**
     * @return mixed
     */
    public function getPersonType()
    {
        return PERSON_USER;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function isPro()
    {
        return $this->account->isPro();
    }

    /**
     * @param $feature
     * @return mixed
     */
    public function hasFeature($feature)
    {
        return $this->account->hasFeature($feature);
    }

    /**
     * @return mixed
     */
    public function isTrial()
    {
        return $this->account->isTrial();
    }

    /**
     * @param null $plan
     * @return mixed
     */
    public function isEligibleForTrial($plan = null)
    {
        return $this->account->isEligibleForTrial($plan);
    }

    /**
     * @return int
     */
    public function maxInvoiceDesignId()
    {
        return $this->hasFeature(FEATURE_MORE_INVOICE_DESIGNS) ? 11 : (Utils::isNinja() ? COUNT_FREE_DESIGNS : COUNT_FREE_DESIGNS_SELF_HOST);
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice', 'seller_id');
    }

    public function getZones()
    {
        $query = DB::table('company_zones')
            ->whereRaw('FIND_IN_SET( id , ? )', array($this->company_zones_id))
            ->whereNull('deleted_at')
            ->get();
        return $query;
    }

    public function getAccountsZones()
    {
        $allowedAccountIds = (isset($this->company_zones_id) && $this->company_zones_id !== null && trim($this->company_zones_id) !== "") ? explode(",", $this->company_zones_id) : null;
        if ($allowedAccountIds !== null) {
            $accounts = Account::select(array('id'))->whereIn('company_zones_id', $allowedAccountIds)->get()->keyBy('id')->keys()->toArray();
            return $accounts;
        } else {
            return [];
        }
    }

    public function getAreas()
    {
        $query = DB::table('company_areas')
            ->select(['id','name'])
            ->whereRaw('FIND_IN_SET( id , ? )', array($this->company_areas_id))
            ->whereNull('deleted_at')
            ->get();
        return $query;
    }

    public function getTraceNotifications()
    {
        $userCurrent = $this->realUser();
        $userAreas = collect($userCurrent->getAreas())->keyBy('id')->keys()->toArray();
        $userAreas = (isset($userAreas) && !empty($userAreas[0])) ? $userAreas : null;
        $allowedAccountIds = isset($userCurrent->company_zones_id) ? explode(",", $userCurrent->company_zones_id) : null;
        $isSuperUser = $userCurrent->is_superuser;
        if ($userCurrent->hasAnyRole(['Nivl III', 'Usuario especial']) || $isSuperUser == 1) {
            $accounts = Account::select(array('id'))->get()->toArray();
        } elseif (isset($allowedAccountIds) && !empty($allowedAccountIds[0])) {
            $accounts = Account::select(array('id'))->whereIn('company_zones_id', $allowedAccountIds)->get()->toArray();
        } else {
            $accounts = array(Auth::user()->account_id);
        };
        $notify = array(
            'traces_is_not_verify' => null,
            'traces_is_not_complete' => null,
        );
        if (!is_null($userAreas)) {
            $tracesRequests = TracesRequest::whereIn('account_id', $accounts)->whereIn('company_areas_id', $userAreas)->get();
            foreach ($tracesRequests as $trace) {
                if ($trace->is_verify == 0) {
                    $notify['traces_is_not_verify'] = 'Tienes Solicitudes de Seguimiento sin Verificar';
                };
                if ($trace->is_complete == 0) {
                    $notify['traces_is_not_complete'] = 'Tienes Solicitudes de Seguimiento sin Completar';
                }
            }
        }
        return $notify;
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->getFullName()) {
            return $this->getFullName();
        } elseif ($this->email) {
            return $this->email;
        } else {
            return 'Guest';
        }
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        } else {
            return '';
        }
    }

    /**
     * @return bool
     */
    public function showGreyBackground()
    {
        return !$this->theme_id || in_array($this->theme_id, [2, 3, 5, 6, 7, 8, 10, 11, 12]);
    }

    /**
     * @return mixed
     */
    public function getRequestsCount()
    {
        return Session::get(SESSION_COUNTER, 0);
    }

    /**
     * @param bool $success
     * @param bool $forced
     * @return bool
     */
    public function afterSave($success = true, $forced = false)
    {
        if ($this->email) {
            return parent::afterSave($success = true, $forced = false);
        } else {
            return true;
        }
    }

    /**
     * @return mixed
     */
    public function getMaxNumClients()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_CLIENTS_PRO;
        }

        if ($this->id < LEGACY_CUTOFF) {
            return MAX_NUM_CLIENTS_LEGACY;
        }

        return MAX_NUM_CLIENTS;
    }

    /**
     * @return mixed
     */
    public function getMaxNumVendors()
    {
        if ($this->hasFeature(FEATURE_MORE_CLIENTS)) {
            return MAX_NUM_VENDORS_PRO;
        }

        return MAX_NUM_VENDORS;
    }

    public function clearSession()
    {
        $date = new \DateTime();
		$date->setTimezone(new \DateTimeZone('America/Tegucigalpa'));
		$fdate = $date->format('Y-m-d');

        $keys = [
            SESSION_USER_ACCOUNTS,
            SESSION_TIMEZONE,
            SESSION_DATE_FORMAT,
            SESSION_DATE_PICKER_FORMAT,
            SESSION_DATETIME_FORMAT,
            SESSION_CURRENCY,
            SESSION_LOCALE,
            SESSION_CURRENT_USER_AUTH,
            SESSION_CURRENT_REAL_USER_AUTH,
            SESSION_YESTERDAY_CASH_COUNT_PASS . $fdate,
            SESSION_CASHCOUNT_PASS . $fdate
        ];

        foreach ($keys as $key) {
            Session::forget($key);
        }
    }

    /**
     * @param $user
     */
    public static function onUpdatingUser($user)
    {
        if ($user->password != $user->getOriginal('password')) {
            $user->failed_logins = 0;
        }

        // if the user changes their email then they need to reconfirm it
        if ($user->isEmailBeingChanged()) {
            $user->confirmed = 0;
            $user->confirmation_code = str_random(RANDOM_KEY_LENGTH);
        }
    }

    /**
     * @param $user
     */
    public static function onUpdatedUser($user)
    {
        if (
            !$user->getOriginal('email')
            || $user->getOriginal('email') == TEST_USERNAME
            || $user->getOriginal('username') == TEST_USERNAME
            || $user->getOriginal('email') == 'tests@bitrock.com'
        ) {
            event(new UserSignedUp());
        }

        event(new UserSettingsChanged($user));
    }

    /**
     * @return bool
     */
    public function isEmailBeingChanged()
    {
        return Utils::isNinjaProd()
            && $this->email != $this->getOriginal('email')
            && $this->getOriginal('confirmed');
    }



    /**
     * Set the permissions attribute on the model.
     *
     * @param  mixed  $value
     * @return $this
     */
    protected function setPermissionsAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['permissions'] = 0;
        } else {
            $bitmask = 0;
            foreach ($value as $permission) {
                if (!$permission) {
                    continue;
                }
                $bitmask = $bitmask | static::$all_permissions[$permission];
            }

            $this->attributes['permissions'] = $bitmask;
        }

        return $this;
    }

    /**
     * Expands the value of the permissions attribute
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getPermissionsAttribute($value)
    {
        $permissions = [];
        foreach (static::$all_permissions as $permission => $bitmask) {
            if (($value & $bitmask) == $bitmask) {
                $permissions[$permission] = $permission;
            }
        }

        return $permissions;
    }

    /**
     * Checks to see if the user has the required permission
     *
     * @param  mixed  $permission Either a single permission or an array of possible permissions
     * @param boolean True to require all permissions, false to require only one
     * @return boolean
     */
    public function hasPermission($permission, $requireAll = false)
    {

        if ($this->is_admin) {
            return true;
        } else if (is_string($permission)) {
            return !empty($this->permissions[$permission]);
        } else if (is_array($permission)) {
            if ($requireAll) {
                return count(array_diff($permission, $this->permissions)) == 0;
            } else {
                return count(array_intersect($permission, $this->permissions)) > 0;
            }
        }

        return false;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function owns($entity)
    {
        return !empty($entity->user_id) && $entity->user_id == $this->id;
    }

    /**
     * @return bool|mixed
     */
    public function filterId()
    {
        return $this->hasPermission('view_all') ? false : $this->id;
    }


    public function caddAddUsers()
    {
        if (!Utils::isNinjaProd()) {
            return true;
        } elseif (!$this->hasFeature(FEATURE_USERS)) {
            return false;
        }

        $account = $this->account;
        $company = $account->company;

        $numUsers = 1;
        foreach ($company->accounts as $account) {
            $numUsers += $account->users->count() - 1;
        }

        return $numUsers < $company->num_users;
    }

    public function canCreateOrEdit($entityType, $entity = false)
    {
        return (($entity && $this->can('edit', $entity))
            || (!$entity && $this->can('create', $entityType)));
    }

    public function realUser()
    {
        if (! Session::has('current_real_user_auth')) {
            $realUserId = Session::get('real_userid');
            if ($realUserId > 0) {
                $user = User::with('role')->find($realUserId);
                Session::flash('current_real_user_auth',$user);
                return $user;
            }
            Session::flash('current_real_user_auth', $this);
            return $this;
        }else{
            return Session::get('current_real_user_auth');
        }
    
    }

    public function is_user_route()
    {
        if (! Session::has('is_current_user_route')) {
            $pass= in_array( $this->realUser()->id,Route::whereNull('deleted')->pluck('user_id')->toArray());
            Session::flash('is_current_user_route', $pass);
            return $pass;
        }else{
            return Session::get('is_current_user_route');
        }
    }

    public function route_users()
    {
        $route = Route::where('user_id', $this->realUser()->id)->whereNull('deleted')->first()->id??'';
        return $route;
    }

    public function employee_route($account_id=null)
    {
        $id = Route::where('account_id', $account_id??$this->realUser()->account_id)->where('deleted', null)->pluck('seller_id')->first()??0;

        return Employee::where('id', $id )->pluck('id')->first();
    } 

    
    public function employee($account_id=null)
    {
        $name = Employee::where('account_id', $account_id)->where('enabled', 1)->first();

        return $name->first_name . ' '. $name->last_name;
    }

    public function employees($account_id=null)
    {
        return Employee::where('account_id', $account_id??$this->account->id)->where('enabled', 1)->get();
    }

    public function clients_account($account_id=null)
    {
        return Client::where('account_id', $account_id)->get();
    }

    public function is_account_route($account_id=null)
    {
        $route = new Route();
        $users =  $route->users();

        return in_array($account_id??Auth::user()->account_id ,Route::whereNull('deleted')->pluck('account_id')->toArray());
    }

    public function invoices_venced_routes() {
        $routes = Route::whereNull('deleted')->select('id', 'account_id')->get();
        $total = [];
        $sum = 0;
    
        foreach ($routes as $route) {
            $clientIds = isset($route->account)?$route->account->clients->pluck('id')->toArray() : [];
    
            $invoice = Invoice::whereIn('client_id', $clientIds)
                ->where('is_credit', true)
                ->whereNotIn('invoice_status_id', [3, 6])
                ->where('invoice_type_id', 1)
                ->where('amount', '>', 1)
                ->where('account_id', '!=', 19)
                ->where('end_date', '<', Carbon::now())
                ->selectRaw('SUM(CASE WHEN end_date < CURDATE() THEN balance ELSE 0 END) as total_venced')
                ->first();
    
            $total[$route->id] = $invoice;
            if ($invoice) {
                $sum += $invoice->total_venced;
            }
        }
    
        foreach ($total as $invoice) {
            if ($invoice && $sum > 0) {
                $invoice->porcentaje = round(($invoice->total_venced / $sum) * 100, 2);
            } else {
                $invoice->porcentaje = 0;
            }
        }
        $routes_venced = $total;
        return $routes_venced;
    }
    

    public function clients_atended($id = null, $fecha_in = null, $fecha_fin = null) {

		$routes_id = [];
		$goal = 0;
		$real_user = Auth::user()->realUser();
        $clientIds = [];
        $employees_id = [];
		if($id){
			$routes = Route::where('id', $id)->get();
		}else{
			if ($real_user->supervised_user_rutes_ids) {
				$routes = Route::where('deleted', null)
					->whereIn('user_id', explode(',', $real_user->supervised_user_rutes_ids))->get();
			}elseif ($real_user->role->name != 'Mayoreo') {
				$routes = Route::where('deleted', null)->get();
			}else{
                $routes = Route::where('id', $this->route_users())->get(); 
            }
		}
		foreach($routes as $route){
			$g = Goal::find($route->account->goal_id??0);
			$goal += $g->total??0;

            $clients = isset($route->account->clients)? $route->account->clients->pluck('id')->toArray():[];
            $clientIds = array_merge($clientIds, $clients);
            $employees_id[] = $route->seller_id;
		}
            $function = function ($query) use ($clientIds, $employees_id) {
                $query/* ->whereIn('client_id', $clientIds) */
                ->whereIn('employee_id',$employees_id);
            };

		$total_clients = count($clientIds);

        $now = 	Carbon::now();
        $nowb = Carbon::now();

        //attended
		$startOfMonth =  $now->firstOfMonth();
        $fecha_in_count = $startOfMonth->format('Y-m-d');

        $total_mayoreo= Invoice::where($function)
            ->whereDate('invoice_date', '>=', $fecha_in_count)
            ->where('amount', '>', '1')
            ->whereNotIn('account_id', [6, 19])//no se traen la tienda de prueba ni la distribucion
            ->where('invoice_status_id', '!=', 3)//no se traen las reembolsadas
			->groupBy('client_id')
            ->havingRaw('SUM(amount) > 2500')
    		->selectRaw('client_id, SUM(amount) as total')->get()->keyBy('client_id');

            if ($nowb->day <= 15) {
                $day = $nowb->firstOfMonth();
    
            } else {
                $day = $nowb->firstOfMonth()->addDays(15);
            }

            $fecha_in = $fecha_in??$day->format('Y-m-d');
            $fecha_fin = $fecha_fin??Carbon::now()->format('Y-m-d');


            //amount
			$total_all = Invoice::where($function)
            //->whereDate('date_changed_credit', '>=', $fecha_in)
            ->join('clients as c', 'c.id', '=', 'invoices.client_id')
             ->leftJoin('routes as ro', 'ro.account_id', '=', 'c.account_id')

            ->whereDate('invoices.invoice_date', '>=', $fecha_in)
            ->whereDate('invoices.invoice_date', '<=', $fecha_fin)
            ->where('invoices.amount', '>', '1')
            ->where(function ($query) {
                $query->where('invoices.in_transit', 0)
                ->orWhereNull('invoices.in_transit');
            })
            ->whereNotIn('invoices.account_id', [6, 19])//no se traen la tienda de prueba ni la distribucion
            ->where('invoices.invoice_status_id', '!=', 3)//no se traen las reembolsadas
            ->where('invoices.invoice_type_id', 1)//solo facturas
			->groupBy('invoices.client_id')
    		->selectRaw('invoices.client_id, SUM(invoices.amount) as total')
            ->get()->keyBy('client_id');

            // Obtener las facturas para cada cliente y agregarlas al resultado
            foreach ($total_all as $client_id => $client) {
                $invoices = Invoice::where($function)
                        ->join('clients as c', 'c.id', '=', 'invoices.client_id')
                        ->join('employees as emp', 'emp.id', '=', 'invoices.employee_id')
                        ->leftJoin('routes as ro', 'ro.account_id', '=', 'c.account_id')
                        ->join('accounts as a', 'a.id', '=', 'invoices.account_id')

                                //->whereDate('invoices.date_changed_credit', '>=', $fecha_in)
                                ->whereDate('invoices.invoice_date', '>=', $fecha_in)
                                ->whereDate('invoices.invoice_date', '<=', $fecha_fin)
                                ->where('invoices.amount', '>', '1')
                                ->where(function ($query) {
                                    $query->where('in_transit', 0)
                                    ->orWhereNull('in_transit');
                                })
                                ->whereNotIn('invoices.account_id', [6, 19]) // no se traen la tienda de prueba ni la distribucion
                                ->where('invoices.invoice_status_id', '!=', 3) // no se traen las reembolsadas
                                ->where('invoices.invoice_type_id', 1) // solo facturas
                                ->where('invoices.client_id', $client_id)
                                ->select('invoices.id','invoices.invoice_number', 'invoices.invoice_date','invoices.date_changed_credit','invoices.amount','a.name as route_name', 
                                'emp.first_name as emp_name' , 'emp.last_name as emp_name2',
                                'invoices.is_credit','invoices.invoice_status_id','invoices.last_payment_date')
                                ->get()
                                ->toArray();

                $total_all[$client_id]->invoices = $invoices;
            }

            //consulta anterior a $fecha_in
            $invoices_old = Invoice::where($function)
                        ->join('clients as c', 'c.id', '=', 'invoices.client_id')
                        ->join('employees as emp', 'emp.id', '=', 'invoices.employee_id')
                        ->leftJoin('routes as ro', 'ro.account_id', '=', 'c.account_id')
                        ->join('accounts as a', 'a.id', '=', 'invoices.account_id')

                                ->whereDate('invoices.invoice_date', '>=', '2024-06-01')
                                ->whereDate('invoices.invoice_date', '<', $fecha_in)
                                ->where('invoices.amount', '>', '1')
                                ->where(function ($query) {
                                    $query->where('in_transit', 0)
                                    ->orWhereNull('in_transit');
                                })
                                ->whereNotIn('invoices.account_id', [6, 19]) // no se traen la tienda de prueba ni la distribucion
                                ->whereNotIn('invoices.invoice_status_id', [3, 6]) // no se traen las reembolsadas ni pagas
                                ->where('invoices.is_credit', false) // solo contado
                                ->where('invoices.invoice_type_id', 1) // solo facturas
                                ->select('a.name','invoices.invoice_number','invoices.invoice_number','invoices.invoice_date','invoices.date_changed_credit','invoices.amount' 
                                )
                                ->get();

            $data =  [
                'invoices_old'=>$invoices_old,
                'total_all'=>$total_all, 
                'total_mayoreo'=>$total_mayoreo, 
                'goal'=>$goal, 
                'total_clients' =>$total_clients,
                'fecha_in' => $fecha_in,
                'fecha_fin' => $fecha_fin
            ];

            return $data;
	}

    public function route_commissions($id = null) {
        $employee_ids=[];
		$goal = 0;
		$real_user = Auth::user()->realUser();
        $clientIds = [];
		if($id){
			$routes = Route::where('id', $id)->get();
		}else{
			if ($real_user->supervised_user_rutes_ids) {
				$routes = Route::where('deleted', null)
					->whereIn('user_id', explode(',', $real_user->supervised_user_rutes_ids))->get();
			}elseif ($real_user->role->name != 'Mayoreo') {
				$routes = Route::where('deleted', null)->get();
			}else{
                $routes = Route::where('id', $this->route_users())->get(); 
            }
		}
		foreach($routes as $route){
			$g = Goal::find($route->account->goal_id??0);
			$goal += $g->total??0;
		    $employees_id[] = $route->seller_id;

            $clients = isset($route->account->clients)? $route->account->clients->pluck('id')->toArray():[];
            $clientIds = array_merge($clientIds, $clients);
		}

            $function = function ($query) use ($clientIds, $employees_id) {
                $query/* ->whereIn('client_id', $clientIds) */
                ->whereIn('employee_id',$employees_id);
            };

        $nowb = Carbon::now();
        //$nowb = Carbon::parse('2023-01-01');

            if ($nowb->day <= 15) {
                $day = $nowb->firstOfMonth();
    
            } else {
                $day = $nowb->firstOfMonth()->addDays(15);
            }

            $invoices_all=[];
                $query = Invoice::with('invoice_items')->where($function)
                                //->whereDate('date_changed_credit', '>=', $day->format('Y-m-d'))
                                ->whereDate('invoice_date', '>=', $day->format('Y-m-d'))
                                ->where('amount', '>', '1')
                                ->where(function ($query) {
                                    $query->where('in_transit', 0)
                                    ->orWhereNull('in_transit');
                                })
                                ->whereNotIn('account_id', [6, 19]) // no se traen la tienda de prueba ni la distribucion
                                ->where('invoice_type_id', 1) // solo facturas
                                ->orderBy('invoice_status_id');

                                $invoices = $query->get();

                                $invoices_oil = clone $query;
                                $invoices_oil = $invoices_oil->where('oil', '>', 0)->get();

                                $invoices_refunded = clone $query;
                                $invoices_refunded = $invoices_refunded->where('total_refunded', '>', 0)->get();

		return ['invoices'=>$invoices, 'goal'=>$goal, 'invoices_oil' => $invoices_oil, 'invoices_refunded'=> $invoices_refunded];
	}

    public function route_collect($id = null){

		$real_user = Auth::user()->realUser();
        $clientIds = [];
        $employees_id = [];
        $money_income = 0;
        $credi_devolucion = 0;
		if($id){
			$routes = Route::where('id', $id)->get();
		}else{
			if ($real_user->supervised_user_rutes_ids) {
				$routes = Route::where('deleted', null)
					->whereIn('user_id', explode(',', $real_user->supervised_user_rutes_ids))->get();
			}elseif ($real_user->role->name != 'Mayoreo') {
				$routes = Route::where('deleted', null)->get();
			}else{
                $routes = Route::where('id', $this->route_users())->get(); 
            }
		}

        foreach($routes as $route){
		    $employees_id[] = $route->seller_id;

            $clients = isset($route->account->clients)? $route->account->clients->pluck('id')->toArray():[];
		    $clientIds = array_merge($clientIds, $clients);
		}

            $function = function ($query) use ($clientIds, $employees_id) {
                $query/* ->whereIn('client_id', $clientIds) */
                ->whereIn('employee_id',$employees_id);
            };

        $now = Carbon::now();

        if ($now->day <= 15) {
            $day1 = $now->copy()->subMonth()->day(27)->format('Y-m-d');
            $day2 = $now->copy()->day(11)->format('Y-m-d');
        } else {
            $day1 = $now->copy()->day(12)->format('Y-m-d');
            $day2 = $now->copy()->day(26)->format('Y-m-d');
        }

        $id_af = IncomeCategory::where('name', 'like', '%(MAYOREO) ABONO A FACTURAS%')->value('id');
        if ($id_af) { 
            foreach($routes as $route){
                $money_income += MoneyIncome::whereDate('document_date', '>=' ,$day1)
                ->whereDate('document_date', '<=' ,$day2)
                ->where('income_category_id', $id_af)
                ->where('account_id', $route->account_id)
                ->whereNotNull('cash_count_id')->sum('amount');
            }
        }

        $type = PaymentType::where('name', 'Credito Por Devolución')->value('id');
        if ($type) { 
            $credi_devolucion += Payment::where($function)
                ->where('payment_type_id', $type)
                ->whereDate('created_at', '>=', $day1)
                ->whereDate('created_at', '<=' ,$day2)
                ->sum('amount');
        }

        $credits_especial = Invoice::where('is_credit', 1)
            ->whereNotIn('invoice_status_id', [3, 6])
            ->whereNotNull('date_changed_credit')
            ->where($function)
            ->where('credit_days', '>',30)
            ->orderBy('end_date','desc')->get();

            foreach($credits_especial as $credit){
            $f = Carbon::parse($credit->date_changed_credit);

                if($credit->credit_days == 45 ){
                    $credit->cuote_1 = $f->addDays(22)->format('Y-m-d');
                    $credit->cuote_2 = $f->addDays(23)->format('Y-m-d');
                    $credit->amount_c = $credit->amount / 2;
                }elseif($credit->credit_days == 60 ){
                    $credit->cuote_1 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_2 = $f->addDays(30)->format('Y-m-d');
                    $credit->amount_c = $credit->amount / 2;

                }elseif($credit->credit_days == 90 ){
                    $credit->cuote_1 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_2 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_3 = $f->addDays(30)->format('Y-m-d');
                    $credit->amount_c = $credit->amount / 3;

                }elseif($credit->credit_days == 120 ){
                    $credit->cuote_1 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_2 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_3 = $f->addDays(30)->format('Y-m-d');
                    $credit->cuote_4 = $f->addDays(30)->format('Y-m-d');
                    $credit->amount_c = $credit->amount / 4;
                }
            }
            $id_especial = $credits_especial->pluck('id');

         $credits = Invoice::where('is_credit', 1)
            ->whereNotIn('invoice_status_id', [3])
            ->whereNotIn('id', $id_especial)
            ->whereNotNull('date_changed_credit')
            ->where($function)
            ->orderBy('end_date','desc')
            ->whereDate('end_date', '>=' ,$day1)
            ->whereDate('end_date', '<=' ,$day2)->get();

        $credits_old = Invoice::where('is_credit', 1)
            ->whereNotIn('invoice_status_id', [3, 6])
            ->whereNotIn('id', $id_especial)
            ->whereNotNull('date_changed_credit')
            ->where($function)
            ->orderBy('end_date','desc')
            ->whereDate('end_date', '<=', $day1)
            ->get(); 

        foreach($credits_old as $old ){
            $old->credit_old = 1;
            $old->amount = $old->balance;
        }

        $pagos = Payment::where($function)
        ->whereDate('created_at', '>=', $day1)->get();

        $credits_paied = Invoice::where('is_credit', 1)
            ->where('invoice_status_id', 6)
            ->whereNotIn('id', $id_especial)
            ->where($function)
             ->whereNotNull('date_changed_credit')
             ->whereDate('last_payment_date', '>=', $day1)
            ->get();

            foreach($credits_paied as $pay ){
                $a = Payment::where('invoice_id', $pay->id)
                ->whereDate('created_at', '>=', $day1)->sum('amount');
                $pay->credit_pay = 1;
                $pay->amount = $a;
            }

            $credits_paied = $credits_paied->merge($credits_old);
            $credits = $credits->merge($credits_paied);

		return [ 'pagos'=>$pagos, 'credits_especial'=>$credits_especial, 'credits'=>$credits, 'date'=> $day2, 'money_income' => $money_income, 'credi_devolucion' => $credi_devolucion];

    }

    public function route_cash_sales($id = null){

		$real_user = Auth::user()->realUser();
        $clientIds = [];
        $employees_id = [];
		if($id){
			$routes = Route::where('id', $id)->get();
		}else{
			if ($real_user->supervised_user_rutes_ids) {
				$routes = Route::where('deleted', null)
					->whereIn('user_id', explode(',', $real_user->supervised_user_rutes_ids))->get();
			}elseif ($real_user->role->name != 'Mayoreo') {
				$routes = Route::where('deleted', null)->get();
			}else{
                $routes = Route::where('id', $this->route_users())->get(); 
            }
		}

        foreach($routes as $route){
            $employees_id[] = $route->seller_id;

            $clients = isset($route->account->clients)? $route->account->clients->pluck('id')->toArray():[];
            $clientIds = array_merge($clientIds, $clients);
		}

        $function = function ($query) use ($clientIds, $employees_id) {
            $query/* ->whereIn('client_id', $clientIds) */
            ->whereIn('employee_id',$employees_id);
        };

        $nowa = Carbon::now();
        $nowb = Carbon::now();
        //$nowb = Carbon::parse('2023-01-01');

        if ($nowb->day <= 15) {
            $day1 = $nowa->firstOfMonth()->format('Y-m-d');
            $day2 = $nowb->firstOfMonth()->addDays(14)->format('Y-m-d');
        } else {
            $day1 = $nowa->firstOfMonth()->addDays(15)->format('Y-m-d');
            $day2 = $nowb->endOfMonth()->format('Y-m-d');
        }

         $query = Invoice::where(function($q){
            $q->where('is_credit', 0)
            ->orWhere('is_credit', null);
         })
         //no devueltas
            ->whereNotIn('invoice_status_id', [3])
            ->where($function)
            ->orderBy('date_changed_credit','desc')
            /* ->whereDate('date_changed_credit', '>=' ,$day1)
            ->whereDate('date_changed_credit', '<=' ,$day2) */
            ->whereDate('invoice_date', '>=' ,$day1)
            ->whereDate('invoice_date', '<=' ,$day2);

            $cash = $query->get();

            $invoices_oil = clone $query;
            $invoices_oil = $invoices_oil->where('oil', '>', 0)->get();

            $invoices_refunded = clone $query;
            $invoices_refunded = $invoices_refunded->where('total_refunded', '>', 0)->get();

            $this->diffDays($cash);
            $this->diffDays($invoices_oil);
            $this->diffDays($invoices_refunded);

		return ['cash'=>$cash, 'date'=> $day2, 'invoices_oil' => $invoices_oil, 'invoices_refunded'=> $invoices_refunded];

    }

    private function diffDays($cs)
    {
        foreach($cs as $c ){

            $dateChangedCredit = trim($c->date_changed_credit??$c->invoice_date);
            $paymentDate = trim($c->payment_date??$c->invoice_date);
            
                $fechaAnterior = new DateTime($dateChangedCredit);
                $fechaActual = new DateTime($paymentDate);
                $c->diferencia = $fechaActual->diff($fechaAnterior)->days;
                $c->aplica = $c->diferencia <= 4? true:false;
        }
    }

    public function permissions()
    {
        $permissions = Session::get('user_permissions');
        return $permissions ? $permissions : [];
    }

    public function _can($code = '')
    {
        $permissions = Session::get('user_permissions');
        $permissions = $permissions ? $permissions : [];
        return isset($permissions[$code]) ? true : false;
//        return in_array($code, $permissions);
    }
    public function hasAnyRole($has_role)
    {
        if (is_string($has_role)) {
            if ($this->role->name == $has_role) {
                return true;
            } else {
                return false;
            }
        } else if (is_array($has_role)) {
            foreach ($has_role as $_role) {
                if ($this->role->name == $_role) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    public function getNameAttribute()
    {
        if (isset($this->first_name) && isset($this->last_name)) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return isset($this->name) ? $this->name : '';
    }
    public function get_role_id($value)
    {
        return UserRole::find($value)->name;
    }
    public function get_allowed_account_ids($value)
    {
        if(isset($value) && !empty($value)){
            $value = explode(",", trim($value, ','));
            $query = DB::table('accounts')
                ->whereIn('id', $value)
                ->whereNull('deleted_at')
                ->pluck('name');
            return implode(", ", $query);
        }
        return $value;
    }
}

User::updating(function ($user) {
    User::onUpdatingUser($user);
});

User::updated(function ($user) {
    User::onUpdatedUser($user);
});
