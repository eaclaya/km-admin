<?php

namespace App\Http\Controllers;

use App\Models\Main\ConditionsSpecialNegotiation;
use App\Models\Main\Payment;
use App\Models\Main\Refund;
use App\Models\Main\SpecialNegotiation;
use App\Services\SpecialNegotiationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SpecialNegotiationsController extends Controller
{
    public function __construct(
        public SpecialNegotiationsService $moduleService,
    ) {}

    public function index()
    {
        $user = Auth::user()->realUser();
        $routes_id = $this->moduleService->getRouteToUser($user);

        return view('special_negotiations.index', ['routes_id' => $routes_id]);
    }

    public function create()
    {
        $data = [];
        $conditions = ConditionsSpecialNegotiation::get();
        $data['conditions'] = $conditions;

        $data['route_select'] = [
            'model' => "App\\Models\\Main\\Route",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'route_id',
        ];
        $data['account_select'] = [
            'model' => "App\\Models\\Main\\Account",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'account_id',
            'set_properties' => [
                [
                    'name' => 'invoice_id',
                    'filters' => [
                        'account_id' => '$selected',
                    ],
                ],
            ]
        ];
        $data['employee_select'] = [
            'model' => "App\\Models\\Main\\Employee",
            'filters'=> ['first_name','last_name', 'id_number'],
            'columnText'=> ['first_name','last_name'],
            'name' => 'employee_id',
        ];
        $data['client_select'] = [
            'model' => "App\\Models\\Main\\Client",
            'filters' => ['name', 'company_name'],
            'columnText' => ['name', 'company_name'],
            'name' => 'client_id',
        ];
        $data['invoice_select'] = [
            'model' => "App\\Models\\Main\\Invoice",
            'filters'=> ['invoice_number'],
            'columnText'=> ['invoice_number', 'created_at', 'amount'],
            'name' => 'invoice_id',
            'is_multiple' => true,
        ];
        return view('special_negotiations.create', $data);
    }

    public function store(Request $request): mixed
    {
        $data = $request->all();
        $this->moduleService->getRepository()->createSpecialNegotiation($data);

        return redirect()->route('special_negotiations.index');
    }

    public function show(string $id)
    {
        $negotiation = $this->moduleService->getRepository()
            ->firstShowSpecialNegotiation($id);
        if (! isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }

        $conditions = ConditionsSpecialNegotiation::get();

        return view(
            'special_negotiations.show',
            [
                'special_negotiation' => $negotiation,
                'conditions' => $conditions,
            ]
        );
    }

    public function edit(string $id)
    {
        $data = [];
        $negotiation = SpecialNegotiation::find($id);
        if (! isset($negotiation)) {
            return redirect()->route('special_negotiations.index');
        }
        $data['special_negotiation'] = $negotiation;

        $conditions = ConditionsSpecialNegotiation::get();
        $data['conditions'] = $conditions;

        $data['route_select'] = [
            'model' => 'App\\Models\\Main\\Route',
            'filters' => ['name'],
            'columnText' => ['name'],
            'name' => 'route_id',
            'optionSelected' => [
                'id' => $negotiation->route_id,
            ],
        ];
        $data['account_select'] = [
            'model' => 'App\\Models\\Main\\Account',
            'filters' => ['name'],
            'columnText' => ['name'],
            'optionSelected' => [
                'id' => $negotiation->account_id,
            ],
            'name' => 'account_id',
        ];
        $data['employee_select'] = [
            'model' => 'App\\Models\\Main\\Employee',
            'filters' => ['first_name', 'last_name', 'id_number'],
            'columnText' => ['first_name', 'last_name'],
            'optionSelected' => [
                'id' => $negotiation->employee_id,
            ],
            'name' => 'employee_id',
        ];
        $data['client_select'] = [
            'model' => 'App\\Models\\Main\\Client',
            'filters' => ['name', 'company_name'],
            'columnText' => ['name', 'company_name'],
            'name' => 'client_id',
            'optionSelected' => [
                'id' => $negotiation->client_id,
            ],
        ];
        $data['invoice_select'] = [
            'model' => 'App\\Models\\Main\\Invoice',
            'filters' => ['invoice_number'],
            'columnText' => ['invoice_number', 'created_at', 'amount'],
            'name' => 'invoice_id',
            'is_multiple' => true,
            'optionSelected' => [
                'id' => $negotiation->invoices->pluck('id')->toArray(),
            ],
        ];
        $data['status_select'] = [
            'array' => [
                '0' => 'Activo',
                '1' => 'Vencido',
            ],
            'optionSelected' => [
                'id' => $negotiation->status,
            ],
            'name' => 'status',
        ];

        return view('special_negotiations.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $this->moduleService->getRepository()->updateSpecialNegotiation($id, $data);

        return redirect()->route('special_negotiations.index');
    }

    public function destroy(string $id)
    {
        //
    }

    public function quotaStore(Request $request)
    {
        $data = $request->all();
        $negotiation = $this->moduleService->createQuotas($data);
        if (! $negotiation) {
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
        if (! $quota) {
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
        if (! isset($payments)) {
            return response()->json(['error' => 'No se encontro pagos'], 404);
        }

        return response()->json(['payments' => $payments], 200);
    }

    public function get_refunds(Request $request, $id)
    {
        $data = $request->all();
        $refunds = Refund::where('invoice_id', $id)->select('id', 'total_refunded', 'refund_date', 'refund_number')->get();
        if (! isset($refunds)) {
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

        if (! $payment) {
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

        if (! $refund) {
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

        if (! $discount) {
            Session::flash('message', 'No se encontro el Descuento');

            return redirect()->back();
        }
        Session::flash('message', 'Descuento Actualizado Correctamente');

        return redirect()->back();
    }

    public function set_credit_record(Request $request, $id)
    {
        $data = $request->all();
        if (! isset($data['credit_record_is_payment'])) {
            $data['credit_record_is_payment'] = 0;
        }
        $negotiation = $this->moduleService->getRepository()->setCreditRecord($id, $data);
        if (! $negotiation) {
            Session::flash('message', 'No se encontro la Negociacion');

            return redirect()->back();
        }
        Session::flash('message', 'Record Asignado Correctamente');

        return redirect()->back();
    }

    public function set_document($id)
    {
        $negotiation = $this->moduleService->getRepository()->setDocument($id);
        if (! $negotiation) {
            Session::flash('message', 'No se encontro la Negociacion');

            return redirect()->back();
        }
        Session::flash('message', 'Documento Asignado Correctamente');

        return redirect()->back();
    }
}
