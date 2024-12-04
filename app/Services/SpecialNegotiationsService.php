<?php

namespace App\Services;

use App\Models\Main\Account;
use App\Models\Main\Route;
use App\Models\Main\SpecialNegotiation;
use App\Repositories\SpecialNegotiationsRepository;

class SpecialNegotiationsService
{
    public function __construct(
        public SpecialNegotiationsRepository $moduleRepository
    ) {}

    public function getRepository(): SpecialNegotiationsRepository
    {
        return $this->moduleRepository;
    }

    public function createQuotas($data)
    {
        $negotiation = SpecialNegotiation::find($data['special_negotiations_id']);
        if (! isset($negotiation)) {
            return false;
        }
        $condition_id = $data['conditions_special_negotiation_id'];
        $condition = $this->moduleRepository->setConditionInToNegotiation(
            $condition_id,
            $data['special_negotiations_id']
        );
        $conditionsRange = $condition->condition_range_array;
        $quotasQty = count($conditionsRange);
        $result = [];
        for ($i = 0; $i < $quotasQty; $i++) {
            $days = $conditionsRange[$i];
            $result[] = [
                'special_negotiations_id' => $data['special_negotiations_id'],
                'account_id' => $data['account_id'],
                'employee_id' => $data['employee_id'],
                'client_id' => $data['client_id'],
                'invoice_id' => $data['invoice_id'][$i],
                'initial_balance' => $data['initial_balance'][$i],
                'monthly_payment' => $data['monthly_payment'][$i],
                'status' => $data['status'][$i],
                'credit_start_at' => $data['credit_start_at'][$i],
                'credit_payment_at' => $data['credit_payment_at'][$i],
                'credit_condition' => $days,
            ];
        }

        return $this->moduleRepository->createQuotas($result, $data['special_negotiations_id']);
    }

    public function getRouteToUser($user)
    {
        $role = $user->role;
        $role_name = $role->name;
        $routes = null;

        if (strpos($role_name, 'Vendedor') != false) {
            $routes = Route::whereNull('deleted')->where('user_id', $user->id)->pluck('id')->toArray();
            if (! isset($routes)) {
                $routes = Route::where('account_id', $user->account_id)->where('deleted', null)->pluck('id')->toArray();
            }
        }

        if (trim($role_name) == 'Supervisor de ventas Mayoreo') {
            $allowedAccountIds = (isset($user->company_zones_id) && $user->company_zones_id !== null && trim($user->company_zones_id) !== '')
                                        ? explode(',', $user->company_zones_id) : null;
            if ($allowedAccountIds !== null) {
                $accounts = Account::whereIn('company_zones_id', $allowedAccountIds)->pluck('id')->toArray();
                $routes = Route::where('account_id', $accounts)->where('deleted', null)->pluck('id')->toArray();
            }
        }

        if (in_array(trim($role_name), ['Finanzas', 'Nivl III', 'Jefe de Ventas Mayoreo', 'AUXILIAR SAC-MAYOREO', 'Usuario especial'])) {
            $routes = Route::where('deleted', null)->pluck('id')->toArray();
        }

        return $routes;
    }
}
