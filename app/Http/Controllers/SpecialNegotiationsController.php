<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Main\SpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\Quota;
use App\Models\Main\Payment;
use Illuminate\Support\Facades\Session;

class SpecialNegotiationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('special_negotiations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('special_negotiations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        unset($data['_token']);
        $negotiation = SpecialNegotiation::create($data);
        $negotiation->invoices()->attach($invoices_ids);
        return redirect()->route('special_negotiations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $negotiation = SpecialNegotiation::find($id);
        if (!isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        return view('special_negotiations.show', ['special_negotiation' => $negotiation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = [];
        $negotiation = SpecialNegotiation::find($id);
        if (!isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        $data['special_negotiation'] = $negotiation;
        $data['route_select'] = [
            'model' => "App\\Models\\Main\\Route",
            'filters' => ['name'],
            'columnText' => ['name'],
            'name' => 'route_id',
            'optionSelected' => [
                'id' => $negotiation->route_id
            ],
        ];
        $data['account_select'] = [
            'model' => "App\\Models\\Main\\Account",
            'filters' => ['name'],
            'columnText' => ['name'],
            'optionSelected' => [
                'id' => $negotiation->account_id
            ],
            'name' => 'account_id',
            'set_properties' => [
                [
                    'name' => 'client_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
                [
                    'name' => 'invoice_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
            ],
        ];
        $data['employee_select'] = [
            'model' => "App\\Models\\Main\\Employee",
            'filters' => ['first_name','last_name', 'id_number'],
            'columnText' => ['first_name','last_name'],
            'optionSelected' => [
                'id' => $negotiation->employee_id
            ],
            'name' => 'employee_id',
        ];
        $data['client_select'] = [
            'model' => "App\\Models\\Main\\Client",
            'filters' => ['name'],
            'columnText' => ['name'],
            'name' => 'client_id',
            'optionSelected' => [
                'id' => $negotiation->client_id
            ],
        ];
        $data['invoice_select'] = [
            'model' => "App\\Models\\Main\\Invoice",
            'filters' => ['invoice_number', 'created_at', 'amount'],
            'columnText' => ['invoice_number', 'created_at', 'amount'],
            'name' => 'invoice_id',
            'is_multiple' => true,
            'optionSelected' => [
                'id' => $negotiation->invoices->pluck('id')->toArray(),
            ],
        ];
        $data['status_select'] = [
            'array' => [
                "0" => 'Activo',
                "1" => 'Vencido',
            ],
            'optionSelected' => [
                'id' => $negotiation->status,
            ],
            'name' => 'status',
        ];
        $data['is_document_select'] = [
            'array' => [
                "0" => 'No',
                "1" => 'Si',
            ],
            'optionSelected' => [
                'id' => $negotiation->is_document,
            ],
            'name' => 'is_document',
        ];
        return view('special_negotiations.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $invoices_ids = $data['invoice_id'];

        unset($data['invoice_id']);
        unset($data['_token']);
        $negotiation = SpecialNegotiation::find($id);
        $negotiation->update($data);
        $negotiation->invoices()->sync($invoices_ids);
        return redirect()->route('special_negotiations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function quotaStore(Request $request)
    {
        $data = $request->all();
        $negotiation = SpecialNegotiation::find($data['special_negotiations_id']);
        if (!isset($negotiation)) {
            Session::flash('message', 'No se encontro la negociación');
            return redirect()->back();
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
        foreach ($result as $value) {
            $invoices = $value['invoice_id'];
            unset($value['invoice_id']);
            $quota = Quota::create($value);
            $quota->invoices()->sync($invoices);
        }
        Session::flash('message', 'Cuotas Generadas Correctamente');
        return redirect()->route('special_negotiations.show', $data['special_negotiations_id']);
    }

    public function quotaUpdate(Request $request, string $id)
    {
        $data = $request->all();
        $quota = Quota::find($id);
        if (!isset($quota)) {
            Session::flash('message', 'No se encontro la cuota');
            return redirect()->back();
        }
        unset($data['_token']);
        $invoices = $data['invoice_id'];
        unset($data['invoice_id']);
        $quota->update($data);
        $quota->invoices()->sync($invoices);

        Session::flash('message', 'Cuota Actualizada Correctamente');
        return redirect()->back();
    }

    public function get_payments(Request $request, $id)
    {
        $data = $request->all();
        $payments = Payment::where('invoice_id',$id)->select('id','amount','payment_date')->get();
        if (!isset($payments)) {
            return response()->json(['error' => 'No se encontro pagos'], 404);
        }
        return response()->json(['payments' => $payments], 200);
    }

    public function paymentStore(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        $specialNegotiationsId = $data["special_negotiations_id"];
        $quotaId = $data["quota_id"];
        $accountId = $data["account_id"];
        $employeeId = $data["employee_id"];
        $clientId = $data["client_id"];

        $invoiceId = $data["invoice_id"];
        $paymentId = $data["payment_id"];

        $paymentAt = $data["payment_at"];

        $quota = Quota::find($quotaId);
        $monthlyPayment = $quota->monthly_payment;
        $days = Carbon\Carbon::now()->diffInDays(Carbon\Carbon::parse($quota->credit_payment_at)) + 1;
        $is_overdue = $days <= 0 ? true : false;

        $payments = PaymentQuota::where('quota_id', $quotaId)->select('mount_balance', 'mount_balance_total', 'final_balance')->get();
        $lastPaymentBalanceTotal = $payments->last() ? $payments->last()->mount_balance_total : 0;
        $lastPaymentBalance = $payments->last() ? $payments->last()->final_balance : 0;

        $paymentAmounts = $payments->sum('mount_balance');

        $mountBalance = $data["mount_balance"];
        $mountTotalBalance = $lastPaymentBalanceTotal + $mountBalance;

        $finalBalance = $monthlyPayment - $mountTotalBalance;

        $insert = [
            'special_negotiations_id' => $specialNegotiationsId,
            'quota_id' => $quotaId,
            'account_id' => $accountId,
            'employee_id' => $employeeId,
            'client_id' => $clientId,
            'invoice_id' => $invoiceId,
            'payment_id' => $paymentId,
            'mount_balance' => $mountBalance,
            'mount_balance_total' => $mountTotalBalance,
            'overdue_balance' => $is_overdue ? $lastPaymentBalance : 0,
            'final_balance' => $finalBalance,
            'payment_at' => $paymentAt,
        ];

        dd($insert);
    }
}
