<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Main\SpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\RefundQuota;
use App\Models\Main\Quota;
use App\Models\Main\Payment;
use App\Models\Main\Refund;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use \Auth;

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
        $negotiation = SpecialNegotiation::where('id', $id)->with([
            'invoices:id,invoice_number,amount',
            'route:id,name', 'account:id,name',
            'employee:id,first_name,last_name',
            'client:id,name,company_name,phone,work_phone,address1',
            'quotas', 'quotas.invoices:id,invoice_number,amount',
            'quotas.payments', 'quotas.discounts',
            'quotas.refunds',
        ])->first();

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
        $quota->activateTracking();
        $quota->setReason($data['reason']);
        unset($data['reason']);
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

    public function get_refunds(Request $request, $id)
    {
        $data = $request->all();
        $refunds = Refund::where('invoice_id',$id)->select('id', 'total_refunded', 'refund_date', 'refund_number')->get();
        if (!isset($refunds)) {
            return response()->json(['error' => 'No se encontro pagos'], 404);
        }
        return response()->json(['refunds' => $refunds], 200);
    }

    public function paymentStore(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $quota = Quota::find($quota_id);
        $monthlyPayment = $quota->monthly_payment;
        $days = Carbon::now()->diffInDays(Carbon::parse($quota->credit_payment_at)) + 1;
        $is_overdue = $days <= 0 ? true : false;
        $data['mount_balance_total'] = 0;
        $data['final_balance'] = 0;
        $data['overdue_balance'] = $is_overdue ? $monthlyPayment : 0;

        PaymentQuota::create($data);
        $this->paymentCalculate($quota_id);

        Session::flash('message', 'Pago Agregado Correctamente');
        return redirect()->back();
    }

    public function paymentUpdate(Request $request, $id)
    {
        $data = $request->all();
        $payment = PaymentQuota::find($id);

        if (!isset($payment)) {
            Session::flash('message', 'No se encontro el pago');
            return redirect()->back();
        }
        unset($data['_token']);
        $payment->activateTracking();
        $payment->setReason($data['reason']);
        unset($data['reason']);
        $payment->update($data);

        $quota_id = $payment->quota_id;
        $this->paymentCalculate($quota_id);
        Session::flash('message', 'Pago Actualizado Correctamente');
        return redirect()->back();
    }

    public function paymentCalculate($quota_id)
    {
        $monthlyPayment = Quota::where('id',$quota_id)->first()->monthly_payment;

        $payments = PaymentQuota::where('quota_id', $quota_id)
            ->select('id','mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastPaymentBalanceTotal = 0;
        $lastFinalBalance = $monthlyPayment;

        foreach ($payments as $payment) {
            $payment->mount_balance_total = floatval( $lastPaymentBalanceTotal + $payment->mount_balance );
            $payment->final_balance = floatval($lastFinalBalance - $payment->mount_balance);
            $payment->save();

            $lastPaymentBalanceTotal = $payment->mount_balance_total;
            $lastFinalBalance = $payment->final_balance;
        }
        $this->refundCalculate($quota_id);
    }

    public function refundStore(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $quota = Quota::find($quota_id);
        $days = Carbon::now()->diffInDays(Carbon::parse($quota->credit_payment_at)) + 1;
        $is_overdue = $days <= 0 ? true : false;

        $paymentCuota = PaymentQuota::where('quota_id', $quota_id)
            ->select('final_balance')
            ->orderBy('id', 'desc')
            ->first();

        $monthlyRefund = 0;
        if(isset($paymentCuota)){
            $monthlyRefund = $paymentCuota->final_balance;
        }

        $data['mount_balance_total'] = 0;
        $data['final_balance'] = 0;
        $data['overdue_balance'] = $is_overdue ? $monthlyRefund : 0;

        RefundQuota::create($data);
        $this->refundCalculate($quota_id);

        Session::flash('message', 'Pago Agregado Correctamente');
        return redirect()->back();
    }

    public function refundUpdate(Request $request, $id)
    {
        $data = $request->all();
        $refund = RefundQuota::find($id);

        if (!isset($refund)) {
            Session::flash('message', 'No se encontro el pago');
            return redirect()->back();
        }
        unset($data['_token']);
        $refund->activateTracking();
        $refund->setReason($data['reason']);
        unset($data['reason']);
        $refund->update($data);

        $quota_id = $refund->quota_id;
        $this->refundCalculate($quota_id);
        Session::flash('message', 'Pago Actualizado Correctamente');
        return redirect()->back();
    }

    public function refundCalculate($quota_id)
    {
        $lastPayment = PaymentQuota::where('quota_id', $quota_id)
            ->select('final_balance', 'mount_balance_total')
            ->orderBy('id', 'desc')
            ->first();

        $lastRefundQuotaBalanceTotal = 0;
        $monthlyRefundQuota = 0;
        if (isset($lastPayment)) {
            $monthlyRefundQuota = $lastPayment->final_balance;
            $lastRefundQuotaBalanceTotal = $lastPayment->mount_balance_total;
        }else{
            $monthlyRefundQuota = Quota::where('id',$quota_id)->first()->monthly_payment;
        }

        $refundQuotas = RefundQuota::where('quota_id', $quota_id)
            ->select('id','mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastFinalBalance = $monthlyRefundQuota;

        foreach ($refundQuotas as $refundQuota) {
            $refundQuota->mount_balance_total = floatval( $lastRefundQuotaBalanceTotal - $refundQuota->mount_balance );
            $refundQuota->final_balance = floatval($lastFinalBalance - $refundQuota->mount_balance);
            $refundQuota->save();

            $lastRefundQuotaBalanceTotal = $refundQuota->mount_balance_total;
            $lastFinalBalance = $refundQuota->final_balance;
        }
    }

    public function discountStore()
    {

    }

    public function discountUpdate()
    {

    }
}
