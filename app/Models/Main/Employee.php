<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use DB;
use DateTime;
use Auth;
class Employee extends ModelDBMain
{

    protected $connection = 'main';
      public $timestamps = false;
      protected $executed_at = null;
      protected $completed_at = null;
      protected $cron = null;
      protected $contractType = [
         1 => 'Temporal',
         2 => 'Permanente',
         3 => 'Contrato Individual',
         4 => 'Otro',
      ];
      protected $genderType = [
         1 => 'Femenino',
         2 => 'Masculino',
      ];

   /**
   * @return mixed
   */
   public function getEntityType()
   {
      return ENTITY_EMPLOYEE;
   }

   public function user(){
      return $this->belongsTo('App\Models\Main\User', 'user_assigned_id', 'id');
   }

   public function account(){
	return $this->belongsTo('App\Models\Main\Account');	
   }
   
   public function clients(){
      return $this->hasMany('App\Models\Main\Client', 'seller_id', 'id');
   }

   public function store_credits(){
      return $this->hasMany('App\Models\Main\StoreCredit', 'employee_id', 'id');
   }

   public function profileEmployee(){
	return $this->belongsTo('App\Models\Main\EmployeeProfile', 'employee_profile_id', 'id');
   }
   public function companyAreas(){
	return $this->belongsTo('App\Models\Main\CompanyAreas', 'company_areas_id', 'id');
   }
   public function companyDepartment(){
	return $this->belongsTo('App\Models\Main\CompanyDepartment', 'company_department_id', 'id');
   }

   public function salaryType(){
	return $this->belongsTo('App\Models\Main\SalaryType', 'salary_type', 'id');
   }

   public function payroll(){
	return $this->hasMany('App\Models\Main\Payroll', 'employee_id', 'id');
   }
   
   public function equipment(){
	return $this->hasMany('App\Models\Main\Equipment', 'employee_id', 'id');
   }
   
   public function vacationDaysTaken(){
      return $this->hasMany('App\Models\Main\VacationDaysTakenEmployees', 'employee_id', 'id');
   }
  
     public function files(){
        return $this->hasMany('App\Models\Main\EmployeeFile', 'employee_id', 'id');
   }

   public function invoices(){
	return $this->hasMany('App\Models\Main\Invoice', 'employee_id', 'id');
   }

   public function travel_expenses(){
	return $this->hasMany('App\Models\Main\TravelExpenses', 'employee_id', 'id');
   }

   public function getEmployeeCommision($commissions, $invoices){
         $total = 0; $totalItemsAmount = 0;
	 $commissionTotals = [];
	 foreach($invoices as $invoice){
	if($invoice->invoice_status_id == 6){
	$invoiceItemsValidAmount = 0;
         foreach($invoice->invoice_items as $item){
            $category_id = ($item->product) ? ($item->product->category_id) : null;
	    $employee_id = $item->invoice->employee_id;
            $type = $item->invoice->client ? $item->invoice->client->type : null;
	    $isValid = false;
	    foreach($commissions as $commission){
	    $invoiceItemsValidAmount = 0;
            $json = json_decode($commission->json, true);
            $condition_match_result = false;
	    $conditions_match = [];
            foreach($json as $index =>  $conditions){
		    $conditions_match[$index] = [];
		    if($conditions){
		    foreach($conditions as $subindex => $condition){
			
			 $conditions_match[$index][$subindex] = [];
                         foreach($condition as $key => $values){
				 $match = false;
                                 if($key == 'condition'){ $match = $values; }
				 else{
                                    foreach($values as $code => $value){
                                        if($key == 'categories' && $code == $category_id){
                                                $match = true;
                                        }
                                        if($key == 'prices' && $value == $type){
                                                $match = true;
                                        }
                                        if($key == 'employees' && $code == $employee_id){
                                                $match = true;
                                        }
				    }
                                }
                                $conditions_match[$index][$subindex][] = $match;
			 }
                    }
                }
	    }
	    $lastCondition = null;
	    $count = count($conditions_match);
	    $firstValue = array_values($conditions_match)[0];
	    if(!is_array($firstValue)){
		    $items = $conditions_match;
		if($count == 1){ $lastCondition = $items[0]; }
                    if($count == 3){
                        if($items[1] === 0){ $lastCondition = $items[0] || $items[2]; }
                        else{ $lastCondition = $items[0] && $items[2]; }
                    }
                    if($count == 5){
                        if($items[1] === 0 && $items[3] == 0){ $lastCondition = $items[0] || $items[2] || $items[4]; }
                        if($items[1] === 1 && $items[3] == 1){ $lastCondition = $items[0] && $items[2] && $items[4]; }
                        if($items[1] === 0 && $items[3] == 1){ $lastCondition = $items[0] || $items[2] && $items[4]; }
                        if($items[1] === 1 && $items[3] == 0){ $lastCondition = $items[0] && $items[2] || $items[4]; }
                    }
	    }else{
	    foreach($conditions_match as $items){
		    $count = count($items);
                    if($count == 1){ $lastCondition = $items[0][0]; }
                    if($count == 3){
                        if($items[1][0] === 0){ $lastCondition = $items[0][0] || $items[2][0]; }
                        else{ $lastCondition = $items[0][0] && $items[2][0]; }
                    }
                    if($count == 5){
                        if($items[1][0] === 0 && $items[3][0] == 0){ $lastCondition = $items[0][0] || $items[2][0] || $items[4][0]; }
                        if($items[1][0] === 1 && $items[3][0] == 1){ $lastCondition = $items[0][0] && $items[2][0] && $items[4][0]; }
                        if($items[1][0] === 0 && $items[3][0] == 1){ $lastCondition = $items[0][0] || $items[2][0] && $items[4][0]; }
                        if($items[1][0] === 1 && $items[3][0] == 0){ $lastCondition = $items[0][0] && $items[2][0] || $items[4][0]; }
                    }
                    if($lastCondition == false){
                        break;
		    }
	    }
	    }

	    $isValid = !$lastCondition;
            if($isValid) {
                    if(isset($commissionTotals[$commission->id]) == false){
			    $commissionTotals[$commission->id] = [
				    'cost' =>  0,
				    'qty' => 0,
				    'total' => 0,
				    'percent' => $commission->commission_percent,
				    'goal' => $commission->goal,
				    'goal_amount_min' => $commission->goal_amount_min,
				    'goal_amount_max' => $commission->goal_amount_max,
				    'commission' => 0,
				    'type' => $commission->type

			    ];
                    }
		    $commissionTotals[$commission->id]['commission'] += floatval($item->cost * $item->qty) * ($commission->commission_percent/100);
		    $commissionTotals[$commission->id]['cost'] += floatval($item->cost);
		    $commissionTotals[$commission->id]['qty'] += floatval($item->qty);
		    $commissionTotals[$commission->id]['total'] += floatval($item->cost * $item->qty);
		    if(isset($commissionTotals[$commission->id]['items']) == false){
			$commissionTotals[$commission->id]['items'] = [];
		    }
		    $commissionTotals[$commission->id]['items'][] = $item;
            }
          }
	 }
	}
	 }
	 foreach($commissionTotals as  $key => $total){
		 if($total['goal_amount_max'] > 0){
			if(!($total['total'] >= $total['goal_amount_min'] && $total['total'] < $total['goal_amount_max'])){
                                unset($commissionTotals[$key]);
                        }

		 }else{
			if(!($total['total'] >= $total['goal_amount_min'])){
				unset($commissionTotals[$key]);
			}
		}
	 }
        return $commissionTotals;
    }

   public function saveCommission($employee, $data){
	 $employee_id = $employee->id;  
	 $payroll = Payroll::where('employee_id', $employee->id)->first();
	 $payroll->sales_amount = 0;
	 $payroll->commission_amount = 0;
	 $payroll->commission_percent = 0;
	 $payroll->commission_global = 0;
	  DB::statement("UPDATE invoices A, invoice_items B SET B.commission =  0, B.commission_global = 0, B.commission_percent = 0, B.commission_percent_global = 0 WHERE A.employee_id = '$employee_id' AND A.id = B.invoice_id AND A.commission_paid = 0");
         foreach($data as $item){
                if($item['type'] == 1){
                        $payroll->commission_amount = $item['commission'];
                        $payroll->commission_percent = $item['percent'];
			$payroll->sales_amount = $item['total'];
			$percent = $item['percent']/100;
			$items = $item['items'];
			foreach($items as $obj){
				$obj->total = $obj->cost * $obj->qty;
				$obj->commission = $obj->total * $percent;
				$obj->commission_percent = $item['percent'];
				$obj->save();
			}
                }

                if($item['type'] == 2){
			$payroll->commission_global = $item['commission'];
			$percent = $item['percent']/100;
                        $items = $item['items'];
                        foreach($items as $obj){
                                $obj->total = $obj->cost * $obj->qty;
                                $obj->commission_global = $obj->total * $percent;
                                $obj->commission_percent_global = $item['percent'];
                                $obj->save();
                        }
                }
        }
	 $payroll->save();
   }

   public function setNextPayrollAmount(){
	$salaryType = $this->salaryType;	
	$payroll = $this->payroll;	
   }

   public function labor_rights()
   {
    return $this->hasMany('App\Models\Main\LaborRight', 'employee_id', 'id');
   }

   public function labor_rights_xiv()
   {
    return $this->hasMany('App\Models\Main\LaborRightsXiv', 'employee_id', 'id');
   }

    public function getNameAttribute()
    {
        if(isset($this->attributes['first_name']) && isset($this->attributes['last_name'])) {
            return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        }
        return isset($this->attributes['name']) ? $this->attributes['name'] : (isset($this->name) ? $this->name : '');
    }

    public function commission_old_products($from_date= null, $to_date= null, $employee_id = null, $acount_id=null) {
        
      $today = \Carbon\Carbon::now();
      $account_id = $acount_id ?? Auth::user()->account_id;
  
      if ($today->day <= 15) {
          $first_day_of_fortnight = $today->copy()->startOfMonth();
          $last_day_of_fortnight = $first_day_of_fortnight->copy()->addDays(14);
      } else {
          $first_day_of_fortnight = $today->copy()->startOfMonth()->addDays(15);
          $last_day_of_fortnight = $today->copy()->endOfMonth();
      }

      $six_months_ago = $last_day_of_fortnight->copy()->subMonths(6);

      $from_date = !empty($from_date)? $from_date : $first_day_of_fortnight->format('Y-m-d');
      $to_date = !empty($to_date) ? $to_date : $last_day_of_fortnight->format('Y-m-d');
      
  
      $employees = Employee::where('account_id', $account_id)
          ->where('is_seller', true)
          ->where('enabled', true)
          ->select("id", "account_id", "first_name", "last_name")
          ->get();
  
      // Consulta principal
      //se traen todas las facturas de esta quincena
      $invoices_query = Invoice::join('invoice_items as ii', 'ii.invoice_id', '=', 'invoices.id')
         ->whereNull('ii.deleted_at')
         ->whereIn('invoices.employee_id', $employees->pluck('id'))
         ->where('invoices.account_id', $account_id)
         ->where('invoices.invoice_type_id', 1)
         ->where('invoices.invoice_status_id', 6)
         ->whereBetween('invoices.created_at', [$from_date, $to_date])
         ->select('invoices.id as invoice_id','invoices.public_id','invoices.invoice_number', 
            'invoices.employee_id',
            'ii.product_id',
            'ii.product_key', 
            DB::raw('MIN(ii.created_at) as first_created_at'),
            'ii.cost',
            'ii.qty', 
            DB::raw('ii.cost * ii.qty as total_cost'))
         ->groupBy('ii.product_key');
         $invoices = $invoices_query->get();

      //no van porque fueron recibidos en los ultimos 6 meses
      $products_t = ProductTracking::whereDate('products_tracking.created_at', '>', $six_months_ago)
      ->whereDate('products_tracking.created_at', '<',  $from_date)
        ->where('reason', '!=', 'Cantidad rebajada en factura')
         ->where('products_tracking.final_account_id', $account_id) 
         ->groupBy('products_tracking.product_key')
         ->whereIn('products_tracking.product_key', $invoices->pluck('product_key'))
         ->pluck('products_tracking.product_key');

      //no van porque fueron recibidos en esta quincena
      $products_u = ProductTracking::whereDate('products_tracking.created_at', '>', $from_date)
      ->whereDate('products_tracking.created_at', '<',  $to_date)
        ->where('reason', '!=', 'Cantidad rebajada en factura')
        ->where('products_tracking.transaction_type', '!=','invoice')
          ->where('products_tracking.final_account_id', $account_id) 
         ->whereIn('products_tracking.product_key', $invoices->pluck('product_key'))
         ->groupBy('products_tracking.product_key')
         ->whereNotIn('products_tracking.product_key', $products_t)
         ->pluck('products_tracking.product_key');

      $no_van = $products_t->merge($products_u)->unique();

     $invoicess = $invoices_query->whereNotIn('product_key', $no_van);
     
      $invoices_old = Invoice::join('invoice_items as ii', 'ii.invoice_id', '=', 'invoices.id')
         ->whereNull('ii.deleted_at')
         ->whereIn('invoices.employee_id', $employees->pluck('id'))
         ->where('invoices.account_id', $account_id)
         ->whereIn('ii.product_key',  $invoicess->pluck('product_key'))
         ->where('invoices.invoice_type_id', 1)
         ->where('invoices.invoice_status_id', 6)
         ->whereDate('invoices.created_at', '>',  $six_months_ago)
         ->whereDate('invoices.created_at', '<',  $from_date)
         ->groupBy('ii.product_key')
         ->select('ii.product_key', 'ii.created_at');
         $invoices_old = $invoices_old->get();

      //$unique_product_keys = $invoices->pluck('product_key')->diff($invoices_old->pluck('product_key'));
      $valors = $invoicess->whereNotIn('product_key', $invoices_old->pluck('product_key'))->get();
      $employees_id = $valors->groupBy('employee_id');

      $data = [
         'valors' => $valors,
         'employees_id' => $employees_id,
         'from_date' => $from_date,
         'to_date' => $to_date
     ];
      return  $data;
  }

}
