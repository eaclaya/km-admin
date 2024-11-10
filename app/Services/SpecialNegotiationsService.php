<?php

namespace App\Services;

use App\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Main\SpecialNegotiation;

use App\Repositories\SpecialNegotiationsRepository;

class SpecialNegotiationsService
{
    public function __construct(
        public SpecialNegotiationsRepository $moduleRepository
    ){}

    public function getRepository(): SpecialNegotiationsRepository
    {
        return $this->moduleRepository;
    }

    public function createQuotas($data)
    {
        $negotiation = SpecialNegotiation::find($data['special_negotiations_id']);
        if (!isset($negotiation)) {
            return false;
        }
        $quotasQty = $data['create_select_quotas_qty'];
        $quotasQty = explode("-", $quotasQty)[1];
        $result = [];
        for ($i=0; $i < $quotasQty; $i++) {
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
                'credit_payment_at' => $data['credit_payment_at'][$i]
            ];
        }
        return $this->moduleRepository->createQuotas($result, $data['special_negotiations_id']);
    }

}
