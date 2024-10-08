<?php

namespace App\Models\Main;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Route extends ModelDBMain
{

    protected $connection = 'main';

	public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

	public function clients(){
		return $this->hasMany('App\Models\Main\RouteClient')->orderBy('name', 'asc');
	}

	public function seller(){
		return $this->belongsTo('App\Models\Main\Employee', 'seller_id', 'id');
	}

	public function user(){
		return $this->belongsTo('App\Models\Main\User');
	}

	public function invoices($clientIds=null){
		if($clientIds){
			$clientIds=[$clientIds];
		}else{
			$clientIds = RouteClient::pluck('client_id')->all();
		}
		
		$invoicesByClient = Invoice::whereIn('client_id', $clientIds)
			->where('invoice_status_id', '!=', 6)
			->where('invoice_status_id', '!=', 3)
			->where('invoice_type_id', '1')
			->where('amount', '>', '1')
			->where('account_id', '!=', '19')
			->select('id','account_id','client_id', 'invoice_number', 'client_type', 'price_type', 'created_at', 'updated_at',
			'last_payment_date', 'is_credit','end_date', 'in_transit', 'amount', 'balance', 'invoice_status_id', 'invoice_date')
			->orderBy('created_at', 'asc')
			->with(['payments', 'refunds'])
			->get()
			->groupBy('client_id');


		return $invoicesByClient;
	}

	public function total($id) {

		$route = Route::find($id);
        $clients = Auth::user()->clients_account($route->account_id);
		$clientIds = $clients->pluck('id');

		$total = Invoice::whereIn('client_id', $clientIds)
			->where('is_credit', true)
			->where('invoice_status_id', '!=', 6)
			->where('invoice_status_id', '!=', 3)
			->where('invoice_type_id', '1')
			->where('amount', '>', '1')
			->where('account_id', '!=', '19')
			->groupBy('client_id')
			->selectRaw('client_id, 
				SUM(CASE WHEN in_transit = 1 THEN balance ELSE 0 END) as total_in_transit_balance,
				SUM(CASE WHEN in_transit = 0 THEN balance ELSE 0 END) as total_balance,
				MIN(end_date) as earliest_invoice_date,
				SUM(CASE WHEN end_date < CURDATE() THEN balance ELSE 0 END) as total_venced')
				->get()->keyBy('client_id');
	
		return $total;
	}

	//users son los usuarios de rutas que estan activos
	public function users(){
		return Route::whereNull('deleted')->pluck('user_id')->toArray();
	}

	function getVisitsRoutes($date)
	{
		$dateE = date('l', strtotime($date));
		setlocale(LC_ALL, 'es_ES');
		$dateE = strftime('%A', strtotime($dateE));
	
		switch ($dateE) {
			case 'Monday':
				$dateE = 'Lunes';
				break;
			case 'Tuesday':
				$dateE = 'Martes';
				break;
			case 'Wednesday':
				$dateE = 'Miércoles';
				break;
			case 'Thursday':
				$dateE = 'Jueves';
				break;
			case 'Friday':
				$dateE = 'Viernes';
				break;
			case 'Saturday':
				$dateE = 'Sábado';
				break;
			case 'Sunday':
				$dateE = 'Domingo';
				break;
			default:
				// Si el día no está en inglés, se deja tal cual
		}
	
		$routes = '';
		$routes = Route::whereNull('deleted')
			->leftJoin('users as us', 'us.id', '=', 'routes.user_id')
			->select(
				'routes.id',
				'routes.account_id',
				'routes.name',
				'us.username as user'
			)
			->orderBy('routes.id', 'asc')
			->get();
	
		$result = [];
		foreach ($routes as $route) {

			if(!isset($route->account_id)){
				continue;
			}

			$clients = $route->account->clients??[];

			$clients_today = $this->clients_visit_today($route->account_id, $date);
			
			$route->num_clients = count($clients);

			$route->num_visits = count($clients_today);


			$visits_outside_day = DB::table('visits')
			     ->join('clients as c', 'c.id', '=', 'visits.client_id')
			     ->join('routes as r', 'r.account_id', '=', 'c.account_id')
			     ->where('r.id', $route->id)
			     ->whereDate('visits.created_at', '=', $date)
			     ->select('visits.created_at', 'c.frequency_day', 'c.route_id')
			     ->groupBy('visits.id')
			     ->get();

			     // Filtrar solo las visitas que ocurran en días diferentes al asignado en 'route_clients'
			     $filtered_visits = [];
			     foreach ($visits_outside_day as $vi) {
			             $created_day_number = date('N', strtotime($vi->created_at)); // Obtener el número del día de la semana para la visita
			             $frequency_day_number = $this->mapDayOfWeek($vi->frequency_day); // Obtener el número del día de la semana para el día asignado

			             if ($created_day_number != $frequency_day_number) {
			                     $filtered_visits[] = $vi;
			             }
			     }

	
			$porcentaje = $route->num_clients ? ($route->num_visits   / $route->num_clients) * 100 : 0;
	
			$result[] = [
				'id' => $route->id,
				'name' => $route->name,
				'user' => $route->user,
				'clients' => $route->num_clients,
				'visits' => $route->num_visits,
				'fecha' => $date,
				'percentage' => round($porcentaje, 2) . '%',
				'other_visits' => count($filtered_visits),
				'clients_today' => $clients_today
			];
		}
	
		return $result;
	}

	public function clients_visit_today($account_id=null, $date=null)
    {
		if(is_string($date)){
			$date = new \DateTime($date);
		}
		$numeroDia = $date->format('N');
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $da = $dias[$numeroDia - 1];
        $week = ceil((int)$date->format('j') / 7);

        $clients = Client::join('visits as vs', 'vs.client_id', '=', 'clients.id')
		->where('clients.account_id', $account_id??$this->realUser()->account_id)
		->whereDate('vs.created_at', '=', $date)
		->select('clients.name', 'clients.company_name', 'clients.phone', 'clients.phone', 'vs.result_visit', 'vs.created_at')
		->get();

        return $clients;
    }

	public function mapDayOfWeek($dayName) {
        switch ($dayName) {
            case 'Lunes':
                return 1;
            case 'Martes':
                return 2;
            case 'Miércoles':
                return 3;
            case 'Jueves':
                return 4;
            case 'Viernes':
                return 5;
            case 'Sábado':
                return 6;
            case 'Domingo':
                return 7;
            default:
                return null;
        }
    }
}
