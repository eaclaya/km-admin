<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Main\SpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\RefundQuota;
use App\Models\Main\Quota;
use App\Models\Main\Payment;
use App\Models\Main\Refund;
use App\Services\SpecialNegotiationsService;
use Carbon\Carbon;
use Auth;

class SpecialNegotiationsController extends Controller
{
    public function __construct(
        public SpecialNegotiationsService $moduleService
    ) {
    }

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
    public function store(Request $request): mixed
    {
        $data = $request->all();
        $this->moduleService->getRepository()->createSpecialNegotiation($data);
        return redirect()->route('special_negotiations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $negotiation = $this->moduleService->getRepository()
            ->firstShowSpecialNegotiation($id);
        if (!isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        return view(
            'special_negotiations.show',
            ['special_negotiation' => $negotiation]
        );
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
        $this->moduleService->getRepository()->updateSpecialNegotiation($id, $data);
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
        $negotiation = $this->moduleService->createQuotas($data);
        if (!$negotiation) {
            Session::flash('message', 'No se encontro la negociación');
            return redirect()->back();
        }
        Session::flash('message', 'Cuotas Generadas Correctamente');
        return redirect()->route('special_negotiations.show', $data['special_negotiations_id']);
    }

    public function quotaUpdate(Request $request, string $id)
    {
        $data = $request->all();

        $quota = $this->moduleService->getRepository()->updateQuota($id, $data);
        if (!$quota) {
            Session::flash('message', 'No se encontro la Cuota');
            return redirect()->back();
        }
        Session::flash('message', 'Cuota Actualizada Correctamente');
        return redirect()->back();
    }

    public function get_payments(Request $request, $id)
    {
        $data = $request->all();
        $payments = Payment::where('invoice_id', $id)->select('id', 'amount', 'payment_date')->get();
        if (!isset($payments)) {
            return response()->json(['error' => 'No se encontro pagos'], 404);
        }
        return response()->json(['payments' => $payments], 200);
    }

    public function get_refunds(Request $request, $id)
    {
        $data = $request->all();
        $refunds = Refund::where('invoice_id', $id)->select('id', 'total_refunded', 'refund_date', 'refund_number')->get();
        if (!isset($refunds)) {
            return response()->json(['error' => 'No se encontro pagos'], 404);
        }
        return response()->json(['refunds' => $refunds], 200);
    }

    public function paymentStore(Request $request)
    {
        $data = $request->all();
        $this->moduleService->getRepository()->createPayment($data);

        Session::flash('message', 'Pago Agregado Correctamente');
        return redirect()->back();
    }

    public function paymentUpdate(Request $request, $id)
    {
        $data = $request->all();
        $payment = $this->moduleService->getRepository()->updatePayment($id, $data);

        if (!$payment) {
            Session::flash('message', 'No se encontro el pago');
            return redirect()->back();
        }
        Session::flash('message', 'Pago Actualizado Correctamente');
        return redirect()->back();
    }

    public function refundStore(Request $request)
    {
        $data = $request->all();
        $refund = $this->moduleService->getRepository()->createRefund($data);

        Session::flash('message', 'Rembolso Agregado Correctamente');
        return redirect()->back();
    }

    public function refundUpdate(Request $request, $id)
    {
        $data = $request->all();
        $refund = $this->moduleService->getRepository()->updateRefund($id, $data);

        if (!$refund) {
            Session::flash('message', 'No se encontro el Rembolso');
            return redirect()->back();
        }
        Session::flash('message', 'Rembolso Actualizado Correctamente');
        return redirect()->back();
    }

    public function discountStore(Request $request)
    {
        $data = $request->all();
        $this->moduleService->getRepository()->createDiscount($data);
        Session::flash('message', 'Descuento Agregado Correctamente');
        return redirect()->back();
    }

    public function discountUpdate(Request $request, $id)
    {
        $data = $request->all();
        $discount = $this->moduleService->getRepository()->updateDiscount($id, $data);

        if (!$discount) {
            Session::flash('message', 'No se encontro el Descuento');
            return redirect()->back();
        }
        Session::flash('message', 'Descuento Actualizado Correctamente');
        return redirect()->back();
    }

    public function set_credit_record(Request $request, $id)
    {
        $data = $request->all();
        $negotiation = $this->moduleService->getRepository()->setCreditRecord($id, $data);
        if (!$negotiation) {
            Session::flash('message', 'No se encontro la Negociacion');
            return redirect()->back();
        }
        Session::flash('message', 'Record Asignado Correctamente');
        return redirect()->back();
    }
}
