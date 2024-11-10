@extends("adminlte::page")

@section("title", "Editar")

@section("content_header")
    <h1>
        Negociacion Especial
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="row d-flex justify-content-between align-items-start">
            <div class="card col-sm-12 col-md-6">
                <h3 class="card-header">
                    {{$special_negotiation->route->name}}
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </h3>
                <div class="card-body">
                    <h4 class="control-label col-12">Descripcion del Credito</h4>
                    <div>
                        <label class="control-label col-5">Tienda</label>
                        {{$special_negotiation->account->name}}
                    </div>
                    <div class="py-2">
                        <ul class="list-group col-12">
                            @foreach ($special_negotiation->invoices as $invoice)
                                <li class="list-group-item d-flex justify-content-between align-items-start" >
                                    <div class="">
                                        Importe del crédito {{$loop->iteration}} - FACT # {{$invoice->invoice_number}}
                                    </div>
                                    <span class="">
                                        {{$invoice->amount_sub_discount_negotiations}}
                                    </span>
                                </li>
                            @endforeach
                            <li class="list-group-item d-flex justify-content-between align-items-start list-group-item-secondary" >
                                <div class="">
                                    IMPORTE TOTAL DEL CREDITO
                                </div>
                                <span class="">
                                    {{$special_negotiation->amount}}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="border-bottom border-top">
                        <label class="control-label col-5">Descuento Otorgado</label>
                        @php
                            $discount_current = 0;
                            $discount_applied = $special_negotiation->discounts->sum('porcent_quotas_discount');
                            $discount_qty = $special_negotiation->discounts->count();
                            if ((isset($discount_applied) && $discount_applied > 0) && (isset($discount_qty) && $discount_qty > 0)) {
                                $discount_current = $discount_applied / $discount_qty;
                            }
                        @endphp
                        {{$discount_applied}} %
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Condicion de Credito</label>
                        @php
                            $credit_condition = 0;
                        @endphp
                        @foreach ($special_negotiation->quotas as $quota)
                            @php
                                $credit_condition += $quota->credit_condition;
                            @endphp
                            @if ($loop->iteration == 1)
                                {{$credit_condition}}
                            @else
                                / {{$credit_condition}}
                            @endif
                        @endforeach
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Fecha de Inicio</label>
                        {{$special_negotiation->created_at}}
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Nros de Facturas</label>
                        @foreach ($special_negotiation->invoices as $invoice)
                            @if ($loop->iteration == 1)
                                {{$invoice->invoice_number}}
                            @else
                                @php
                                    $arrNumber = explode( "-", $invoice->invoice_number);
                                @endphp
                                / {{ array_pop( $arrNumber ) }}
                            @endif
                        @endforeach
                    </div>
                    <div class="border-bottom  bg-light">
                        <label class="control-label col-5">Revision Record Créditicio</label>
                        L {{$special_negotiation->credit_record}}
                    </div>
                    <div class="border-bottom list-group-item-secondary">
                        <label class="control-label col-5">TOTAL CREDITO+REVISION HISTORIAL</label>
                        L {{$special_negotiation->amount + $special_negotiation->credit_record}}
                    </div>
                    <div class="border-bottom bg-light">
                        <label class="control-label col-5">Pago mensual sin descuento otorgado</label>
                        L {{$special_negotiation->quotas->first()->monthly_payment}}
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Numero de Pagos</label>
                        {{$special_negotiation->quotas->count()}} Quotas
                    </div>
                    <div class="border-bottom list-group-item-secondary">
                        <label class="control-label col-5">Descuento total otorgado</label>
                        @php $discount_applied = $special_negotiation->discounts->sum('discount_applied') @endphp
                        L {{$discount_applied}}
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Descuento por pago otorgado</label>
                        @forelse ($special_negotiation->discounts as $discount)
                            @if ($loop->iteration > 1) / @endif {{$discount->discount_applied}}
                        @empty
                            L 0
                        @endforelse
                    </div>
                    <div class="border-bottom bg-light">
                        <label class="control-label col-5">Importe total del crédito - descuento</label>
                        {{$special_negotiation->amount - $discount_applied}}
                    </div>
                    <div class="border-bottom  list-group-item-secondary">
                        <label class="control-label col-5">Pago mensual con descuento otorgado</label>
                        @forelse ( $special_negotiation->quotas as $quota )
                            @php
                                $current_discount = $quota->discounts->sum('discount_applied');
                                $monthly_payment = $quota->monthly_payment - $current_discount;
                            @endphp
                            @if ($loop->iteration > 1) / @endif L {{ $monthly_payment }}
                        @empty
                            L 0
                        @endforelse
                    </div>
                </div>
            </div>

            <div class=" col-sm-12 col-md-6 col-lg-4">
                <div class="card col-md-12">
                    <h4 class="card-header">
                        Asesor: {{$special_negotiation->employee->name}}
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </h4>
                    <div class="card-body">
                        <div class="border-bottom list-group-item-secondary">
                            <label class="control-label col-5">Empresa</label>
                            {{$special_negotiation->client->company_name}}
                        </div>
                        <div class="border-bottom border-top">
                            <label class="control-label col-5">Cliente</label>
                            {{$special_negotiation->client->name}}
                        </div>
                        <div class="border-bottom border-top">
                            <label class="control-label col-5">Telefonos</label>
                            {{$special_negotiation->client->phone}} {{$special_negotiation->client->work_phone ? " / ".$special_negotiation->client->work_phone : ""}}
                        </div>
                        <div class="border-bottom border-top">
                            <label class="control-label col-5">Direccion</label>
                            {{$special_negotiation->client->address1}}
                        </div>
                    </div>
                </div>
                <div class="card col-md-12">
                    <div class="card-body">
                        <div class="border-bottom">
                            <label class="control-label col-5">Pendiente en Factura</label>
                            @php
                                $result = 0;
                                foreach ($special_negotiation->quotas as $quota) {
                                    $monthly_payment = $quota->monthly_payment;
                                    $total_payments = $quota->payments->sum('mount_balance');
                                    $total_refunds = $quota->refunds->sum('mount_balance');
                                    $total_discounts = $quota->discounts->sum('discount_applied');
                                    $current_result = $monthly_payment - ($total_payments + $total_discounts + $total_refunds);
                                    $current_result = $current_result > 0 ? $current_result : 0;
                                    $result += $current_result;
                                }
                            @endphp
                            L {{$result}}
                        </div>
                        <div class="border-bottom  bg-light">
                            <label class="control-label col-5">Revision Record Créditicio</label>
                            L {{$special_negotiation->credit_record}} -
                            <a type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#creditRecordModal" >
                                Asignar
                            </a>
                        </div>
                        <div class="border-bottom list-group-item-secondary">
                            <label class="control-label col-5">Total Saldo Pendiente</label>
                            L 0,00 (pendiente)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                Cuotas -
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createQuotaModal">
                    Crear Cuotas
                </button>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table text-center table-responsive">
                    <thead>
                        <tr>
                            <th>N.º de Pago</th>
                            <th>N.º de Factura</th>
                            <th>Fecha de Inicio de Crédito</th>
                            <th>Fecha de Pagos / Condiciones</th>
                            <th>Dias Transcurridos</th>
                            <th>Inicio Saldo</th>
                            <th>Pago Mensual Sin Descuento</th>
                            <th>Estado de la Couta</th>
                            <th>N.º de Pago</th>
                            <th>Fecha de Pago Realizada</th>
                            <th>Monto Abonado</th>
                            <th>Monto Total Abonado</th>
                            <th>Descuento Aplicado</th>
                            <th>Monto Vencido</th>
                            <th>Saldo Final</th>
                            <th>Historial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($special_negotiation->quotas as $quota)
                            <tr class="table-secondary">
                                <td>
                                    {{$loop->iteration}}
                                    <br>
                                    <a class="btn btn-primary btn-sm"
                                        onclick="editQuota('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$quota->credit_start_at}}*-*{{$quota->credit_payment_at}}*-*{{$quota->initial_balance}}*-*{{$quota->monthly_payment}}*-*{{$quota->status}}')"
                                    >
                                        Editar
                                    </a>
                                </td>
                                <td>
                                    @foreach ($quota->invoices as $invoice)
                                        @if ($loop->iteration == 1)
                                            {{$invoice->invoice_number}}
                                        @else
                                            @php
                                                $arrNumber = explode( "-", $invoice->invoice_number);
                                            @endphp
                                            / {{ array_pop( $arrNumber ) }}
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    {{trim($quota->credit_start_at)}}
                                </td>
                                <td>
                                    {{trim($quota->credit_payment_at)}}
                                </td>
                                <td>
                                    @php
                                        $text = "";
                                        $days = Carbon\Carbon::now()->diffInDays(Carbon\Carbon::parse($quota->credit_payment_at)) + 1;
                                        if ($days > 0) {
                                            $text = '<div class="bg-success" > Dias restantes: '. intval(ceil($days)) . '</div>';
                                        }else{
                                            $text = '<div class="bg-danger" > Dias Pasados: '. intval(ceil($days)) . '</div>';
                                        }
                                    @endphp
                                    {!! $text !!}
                                </td>
                                <td>
                                    {{trim($quota->initial_balance)}}
                                </td>
                                <td>
                                    {{trim($quota->monthly_payment)}}
                                </td>
                                <td>
                                    @if($quota->status == 0)
                                        <div class="bg-success">
                                            Activo
                                        </div>
                                    @elseif($quota->status == 1)
                                        <div class="bg-primary">
                                            Pagado
                                        </div>
                                    @else
                                        <div class="bg-danger">
                                            Vencido
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" onclick="addPayment('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}')">
                                        Agregar Pago
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-sm" onclick="addRefund('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}')">
                                        Agregar Rembolso
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" onclick="addDiscount('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$quota->monthly_payment}}')">
                                        Agregar Descuento
                                    </a>
                                </td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>
                                    <a class="btn btn-outline-secondary btn-sm" onclick="showTracking('{{$quota->getEntityType()}}','{{$quota->id}}')">
                                        historial
                                    </a>
                                </td>
                            <tr>
                            @forelse ($quota->payments as $payment)
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <a class="btn btn-outline-primary btn-sm"
                                            onclick="editPayment('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$payment->id}}*-*{{$payment->mount_balance}}*-*{{$payment->create_payment_at}}*-*{{$payment->invoice_id}}')">
                                            Editar
                                        </a>
                                    </td>
                                    <td class="font-weight-bold">
                                        Pago -  {{$loop->iteration}}
                                    </td>
                                    <td>{{$payment->payment_at}}</td>
                                    <td>{{$payment->mount_balance}}</td>
                                    <td>{{$payment->mount_balance_total}}</td>
                                    <td></td>
                                    <td>{{$payment->overdue_balance}}</td>
                                    <td>{{$payment->final_balance}}</td>
                                    <td>
                                        <a class="btn btn-outline-secondary btn-sm" onclick="showTracking('{{$payment->getEntityType()}}','{{$payment->id}}')">
                                            historial
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                Sin Pagos
                            @endforelse
                            @forelse ($quota->refunds as $refund)
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <a class="btn btn-outline-warning btn-sm"
                                            onclick="editRefund('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$refund->id}}*-*{{$refund->mount_balance}}*-*{{$refund->refund_at}}*-*{{$refund->invoice_id}}')">
                                            Editar
                                        </a>
                                    </td>
                                    <td class="font-weight-bold">
                                        Rembolso - {{$loop->iteration}}
                                    </td>
                                    <td>{{$refund->refund_at}}</td>
                                    <td>{{$refund->mount_balance}}</td>
                                    <td>{{$refund->mount_balance_total}}</td>
                                    <td></td>
                                    <td>{{$refund->overdue_balance}}</td>
                                    <td>{{$refund->final_balance}}</td>
                                    <td>
                                        <a class="btn btn-outline-secondary btn-sm" onclick="showTracking('{{$refund->getEntityType()}}','{{$refund->id}}')">
                                            historial
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                Sin Rembolsos
                            @endforelse
                            @forelse ($quota->discounts as $discount)
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <a class="btn btn-outline-info btn-sm"
                                            onclick="editDiscount('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$quota->monthly_payment}}*-*{{$discount->id}}*-*{{$discount->porcent_quotas_discount}}*-*{{$discount->invoice_id}}')">
                                            Editar
                                        </a>
                                    </td>
                                    <td class="font-weight-bold">
                                        Descuento - {{$loop->iteration}}
                                    </td>
                                    <td>{{$discount->created_at->format('Y-m-d')}}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>{{$discount->discount_applied}}</td>
                                    <td>-</td>
                                    <td>{{$discount->final_balance}}</td>
                                    <td>
                                        <a class="btn btn-outline-secondary btn-sm" onclick="showTracking('{{$discount->getEntityType()}}','{{$discount->id}}')">
                                            historial
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                Sin Descuentos
                            @endforelse
                        @empty
                            Cuotas por Agregar
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Quota Modals --}}
    <div class="modal fade" id="createQuotaModal" tabindex="-1" aria-labelledby="createQuotaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createQuotaModalLabel">Crear Cuotas</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="{{ route('special_negotiations.quota.store') }}" method="POST" multipart="multipart/form-data">
                        @csrf
                        <input type="hidden" name="special_negotiations_id" value="{{$special_negotiation->id}}">
                        <input type="hidden" name="account_id" value="{{$special_negotiation->account_id}}">
                        <input type="hidden" name="employee_id" value="{{$special_negotiation->employee_id}}">
                        <input type="hidden" name="client_id" value="{{$special_negotiation->client->id}}">
                        <div class="col-md-12 mb-3 border-bottom">
                            <div class="mb-3" >
                                <legend>Seleccione una condicion:</legend>

                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="create_select_quotas_qty" class="form-check-input" value="45-2" checked />
                                        45 Dias - 2 Cuotas
                                    </label>
                                </div>

                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="create_select_quotas_qty" class="form-check-input" value="60-2" />
                                        60 Dias - 2 Cuotas
                                    </label>
                                </div>

                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="create_select_quotas_qty" class="form-check-input" value="90-3" />
                                        90 Dias - 3 Cuotas
                                    </label>
                                </div>

                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" name="create_select_quotas_qty" class="form-check-input" value="120-4" />
                                        120 Dias - 4 Cuotas
                                    </label>
                                </div>
                            </div>
                        </div>
                        @for ($i = 0; $i < 4; $i++)
                            <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                                <div class="col-md-12">
                                    <h4>Cuota {{$i + 1}}</h4>
                                </div>

                                <div class="col-md-3">
                                    <label for="create_invoice_id_{{$i}}" class="form-label">Factura:</label>
                                    <select name="invoice_id[{{$i}}][]" id="create_invoice_id_{{$i}}" class="form-control" multiple required>
                                        @foreach ($special_negotiation->invoices as $invoice)
                                            <option value="{{$invoice->id}}">{{$invoice->invoice_number}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    @php
                                        $initialBalance = 0;
                                        if ($i == 0) {
                                            $quotasCount = $special_negotiation->quotas->count();
                                            $initialBalance = $special_negotiation->amount;
                                            if ($quotasCount > 0 && $initialBalance > 0) {
                                                $initialBalance = $initialBalance / $quotasCount;
                                            }
                                        }
                                    @endphp
                                    <label for="create_initial_balance_{{$i}}" class="form-label">Saldo inicial:</label>
                                    <input
                                        type="number" class="form-control" id="create_initial_balance_{{$i}}"
                                        name="initial_balance[{{$i}}]" value="{{$initialBalance}}" step="0.01"
                                        required @if($i == 0) readonly @endif
                                    />
                                </div>

                                <div class="col-md-3">
                                    <label for="create_monthly_payment_{{$i}}" class="form-label"> Pago mensual: </label>
                                    <input type="number" class="form-control" id="create_monthly_payment_{{$i}}" name="monthly_payment[{{$i}}]" step="0.01" required />
                                </div>

                                <div class="col-md-3">
                                    <label for="create_status_{{$i}}" class="form-label">Estado:</label>
                                    <select class="form-control" id="create_status_{{$i}}" name="status[{{$i}}]">
                                        <option value="0">Activo</option>
                                        <option value="1">Pagado</option>
                                        <option value="2">Vencido</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="create_credit_start_at_{{$i}}" class="form-label">Fecha de inicio de crédito:</label>
                                    <input type="date" class="form-control" id="create_credit_start_at_{{$i}}" name="credit_start_at[{{$i}}]" value="{{ date('Y-m-d') }}" required />
                                </div>

                                <div class="col-md-3">
                                    <label for="create_credit_payment_at_{{$i}}" class="form-label">
                                        Fecha de pago de crédito:
                                    </label>
                                    <input type="date" class="form-control" id="create_credit_payment_at_{{$i}}" name="credit_payment_at[{{$i}}]" required />
                                </div>
                            </div>
                        <hr>
                        @endfor

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="editQuotaModal" tabindex="-1" aria-labelledby="editQuotaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editQuotaModalLabel">Editar Cuota</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id="editQuotaForm" >
                        @csrf
                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-3">
                                <label for="edit_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id[]" id="edit_invoice_id" class="form-control" multiple required>
                                    @foreach ($special_negotiation->invoices as $invoice)
                                        <option value="{{$invoice->id}}">{{$invoice->invoice_number}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="edit_initial_balance" class="form-label">Saldo inicial:</label>
                                <input
                                    type="number" class="form-control" id="edit_initial_balance"
                                    name="initial_balance" value="{{$initialBalance}}"
                                    step="0.01" readonly required
                                />
                            </div>

                            <div class="col-md-3">
                                <label for="edit_monthly_payment" class="form-label"> Pago mensual: </label>
                                <input type="number" class="form-control" id="edit_monthly_payment" name="monthly_payment" step="0.01" required />
                            </div>

                            <div class="col-md-3">
                                <label for="edit_status" class="form-label">Estado:</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="0">Activo</option>
                                    <option value="1">Pagado</option>
                                    <option value="2">Vencido</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="edit_credit_start_at" class="form-label">Fecha de inicio de crédito:</label>
                                <input type="date" class="form-control" id="edit_credit_start_at" name="credit_start_at" required />
                            </div>

                            <div class="col-md-3">
                                <label for="edit_credit_payment_at" class="form-label">
                                    Fecha de pago de crédito:
                                </label>
                                <input type="date" class="form-control" id="edit_credit_payment_at" name="credit_payment_at" required />
                            </div>

                            <div class="col-md-6">
                                <label for="reason" class="form-label">Rason del Cambio:</label>
                                <input type="text" class="form-control" id="reason" name="reason" maxlength="50" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    {{-- Payment Modals --}}
    <div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createPaymentModalLabel">Crear Pago</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='createPaymentForm' action="{{route('special_negotiations.payment.store')}}" >
                        @csrf
                        <input type="hidden" name="special_negotiations_id" value="{{$special_negotiation->id}}">
                        <input type="hidden" name="account_id" value="{{$special_negotiation->account_id}}">
                        <input type="hidden" name="employee_id" value="{{$special_negotiation->employee_id}}">
                        <input type="hidden" name="client_id" value="{{$special_negotiation->client->id}}">
                        <input type="hidden" name="quota_id" id="create_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Pago</h4>
                            </div>
                            <div class="col-md-3">
                                <label for="create_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id" id="create_invoice_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="create_payment_id" class="form-label">Pagos:</label>
                                <select name="payment_id" id="create_payment_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="create_mount_balance" class="form-label">Monto Abonado:</label>
                                <input
                                    type="number" class="form-control" id="create_mount_balance"
                                    name="mount_balance" step="0.01"
                                    required
                                />
                            </div>
                            <div class="col-md-3">
                                <label for="create_payment_at" class="form-label">Fecha de pago:</label>
                                <input type="date" class="form-control" id="create_payment_at" name="payment_at" required />
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editPaymentModalLabel">Editar Pago</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='editPaymentForm'>
                        @csrf
                        <input type="hidden" name="quota_id" id="edit_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Pago</h4>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_quota_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id" id="edit_quota_invoice_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_payment_id" class="form-label">Pagos:</label>
                                <select name="payment_id" id="edit_payment_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_mount_balance" class="form-label">Monto Abonado:</label>
                                <input
                                    type="number" class="form-control" id="edit_mount_balance"
                                    name="mount_balance" step="0.01"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label for="edit_payment_at" class="form-label">Fecha de pago:</label>
                                <input type="date" class="form-control" id="edit_payment_at" name="payment_at" required />
                            </div>
                            <div class="col-md-4">
                                <label for="reason" class="form-label">Rason del Cambio:</label>
                                <input type="text" class="form-control" id="reason" name="reason" maxlength="50" required />
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    {{-- Refund Modals --}}
    <div class="modal fade" id="createRefundModal" tabindex="-1" aria-labelledby="createRefundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createRefundModalLabel">Crear Rembolso</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='createRefundForm' action="{{route('special_negotiations.refund.store')}}" >
                        @csrf
                        <input type="hidden" name="special_negotiations_id" value="{{$special_negotiation->id}}">
                        <input type="hidden" name="account_id" value="{{$special_negotiation->account_id}}">
                        <input type="hidden" name="employee_id" value="{{$special_negotiation->employee_id}}">
                        <input type="hidden" name="client_id" value="{{$special_negotiation->client->id}}">
                        <input type="hidden" name="quota_id" id="create_refund_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Pago</h4>
                            </div>
                            <div class="col-md-3">
                                <label for="create_refund_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id" id="create_refund_invoice_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="create_refund_id" class="form-label">Rembolsos:</label>
                                <select name="refund_id" id="create_refund_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="create_refund_mount_balance" class="form-label">Monto Abonado:</label>
                                <input
                                    type="number" class="form-control" id="create_refund_mount_balance"
                                    name="mount_balance" step="0.01"
                                    required
                                />
                            </div>
                            <div class="col-md-3">
                                <label for="create_refund_at" class="form-label">Fecha de pago:</label>
                                <input type="date" class="form-control" id="create_refund_at" name="refund_at" required />
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="editRefundModal" tabindex="-1" aria-labelledby="editRefundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editRefundModalLabel">Editar Rembolso</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='editRefundForm'>
                        @csrf
                        <input type="hidden" name="quota_id" id="edit_refund_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Pago</h4>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_refund_quota_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id" id="edit_refund_quota_invoice_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_refund_id" class="form-label">Pagos:</label>
                                <select name="refund_id" id="edit_refund_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_refund_mount_balance" class="form-label">Monto Abonado:</label>
                                <input
                                    type="number" class="form-control" id="edit_refund_mount_balance"
                                    name="mount_balance" step="0.01"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label for="edit_refund_at" class="form-label">Fecha de pago:</label>
                                <input type="date" class="form-control" id="edit_refund_at" name="refund_at" required />
                            </div>
                            <div class="col-md-4">
                                <label for="reason" class="form-label">Rason del Cambio:</label>
                                <input type="text" class="form-control" id="reason" name="reason" maxlength="50" required />
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    {{-- Discount Modals --}}
    <div class="modal fade" id="createDiscountModal" tabindex="-1" aria-labelledby="createDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createDiscountModalLabel">Crear Descuento</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='createDiscountForm' action="{{route('special_negotiations.discount.store')}}" >
                        @csrf
                        <input type="hidden" name="special_negotiations_id" value="{{$special_negotiation->id}}">
                        <input type="hidden" name="account_id" value="{{$special_negotiation->account_id}}">
                        <input type="hidden" name="employee_id" value="{{$special_negotiation->employee_id}}">
                        <input type="hidden" name="client_id" value="{{$special_negotiation->client->id}}">
                        <input type="hidden" name="quota_id" id="create_discount_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Descuento</h4>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-3">
                                    <label for="create_discount_invoice_id" class="form-label">Factura:</label>
                                    <select name="invoice_id" id="create_discount_invoice_id" class="form-control" required>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="create_porcent_quotas_discount" class="form-label">Porcentaje de Descuento:</label>
                                    <input
                                        type="number" class="form-control" id="create_porcent_quotas_discount"
                                        name="porcent_quotas_discount" step="0.01"
                                        required
                                    />
                                </div>

                                <div class="col-md-3">
                                    <label for="create_discount_applied" class="form-label">Descuento Aplicado:</label>
                                    <input
                                        type="number" class="form-control" id="create_discount_applied"
                                        name="discount_applied" step="0.01"
                                        required readonly
                                    />
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="col-md-12 row">
                                <div class="col-md-3">
                                    <label for="create_discount_total_amount" class="form-label">Importe Total de Cuota:</label>
                                    <input type="number" value="0"
                                        id="create_discount_total_amount"
                                        class="form-control" disabled readonly
                                    >
                                </div>
                                <div class="col-md-3">
                                    <label for="create_discount_total_amount_sub_porcent" class="form-label">Importe Total Menos Descuento:</label>
                                    <input type="number" value="0"
                                        id="create_discount_total_amount_sub_porcent"
                                        class="form-control" disabled readonly
                                    >
                                </div>
                                <div class="col-md-3">
                                    <label for="create_discount_amount_invoice" class="form-label">Importe de Factura:</label>
                                    <input type="number" value="0" id="create_discount_amount_invoice" class="form-control" disabled readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="create_discount_amount_invoice_sub_porcent" class="form-label">Imp. de Fac. Menos Descuento:</label>
                                    <input type="number" value="0" id="create_discount_amount_invoice_sub_porcent" class="form-control" disabled readonly>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editDiscountModalLabel">Editar Descuento</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" multipart="multipart/form-data" id='editDiscountForm' >
                        @csrf
                        <input type="hidden" name="quota_id" id="edit_discount_quota_id" >

                        <div class="col-md-12 row py-3 mb-3 border-bottom border-top">
                            <div class="col-md-12">
                                <h4>Crear Descuento</h4>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-3">
                                    <label for="edit_discount_invoice_id" class="form-label">Factura:</label>
                                    <select name="invoice_id" id="edit_discount_invoice_id" class="form-control" required>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_porcent_quotas_discount" class="form-label">Porcentaje de Descuento:</label>
                                    <input
                                        type="number" class="form-control" id="edit_porcent_quotas_discount"
                                        name="porcent_quotas_discount" step="0.01"
                                        required
                                    />
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_discount_applied" class="form-label">Descuento Aplicado:</label>
                                    <input
                                        type="number" class="form-control" id="edit_discount_applied"
                                        name="discount_applied" step="0.01"
                                        required readonly
                                    />
                                </div>
                                <div class="col-md-3">
                                    <label for="reason" class="form-label">Rason:</label>
                                    <input
                                        type="text" class="form-control" id="reason"
                                        name="reason"
                                        required
                                    />
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="col-md-12 row">
                                <div class="col-md-3">
                                    <label for="edit_discount_total_amount" class="form-label">Importe Total de Cuota:</label>
                                    <input type="number" value="0"
                                        id="edit_discount_total_amount"
                                        class="form-control" disabled readonly
                                    >
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_discount_total_amount_sub_porcent" class="form-label">Importe Total Menos Descuento:</label>
                                    <input type="number" value="0"
                                        id="edit_discount_total_amount_sub_porcent"
                                        class="form-control" disabled readonly
                                    >
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_discount_amount_invoice" class="form-label">Importe de Factura:</label>
                                    <input type="number" value="0" id="edit_discount_amount_invoice" class="form-control" disabled readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_discount_amount_invoice_sub_porcent" class="form-label">Imp. de Fac. Menos Descuento:</label>
                                    <input type="number" value="0" id="edit_discount_amount_invoice_sub_porcent" class="form-control" disabled readonly>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-3">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    {{-- tracking modal --}}
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="trackingModalLabel">Historial</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <livewire:Datatables.tracking-table :current_model="$special_negotiation->getEntityType()" :id="$special_negotiation->id" />
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    {{-- credit record modal --}}
    <div class="modal fade" id="creditRecordModal" tabindex="-1" aria-labelledby="creditRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="creditRecordModalLabel">Record Crediticio</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form action="{{ route('special_negotiations.set_credit_record', $special_negotiation->id) }}" method="get">
                        <div class="row justify-content-center">
                            <input type="number" class="form-control col-md-2" step="0.01" name="credit_record" id="credit_record" value="{{$special_negotiation->credit_record}}">
                            <button type="submit" class="btn btn-primary btn-sm col-md-2 offset-md-1">Asignar Record Crediticio</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    <script>
        let days = 45;
        let quotasQty = 2;
        let invoices = {!! json_encode($special_negotiation->invoices->map(function($item) { return ['id' => $item->id, 'invoice_number' => $item->invoice_number, 'amount' => $item->amount_sub_discount_negotiations]; })) !!};

        $(document).ready(function() {
            conditionChange();
        });

        // Quotas

        $("#create_credit_start_at_0").change(function() {
            let createCreditStartAt = $(this).val();
            conditionChange();
        });

        $('input[name="create_select_quotas_qty"]').change(function() {
            var thisValue = $(this).val();
            var parts = thisValue.split('-');
            days = parseInt(parts[0]) + 1;
            quotasQty = parseInt(parts[1]);

            conditionChange();
        });

        function conditionChange() {
            let daysAdd = (Math.round((days + 1) / quotasQty)  * 86400000);
            let initialBalanceVal = $('#create_initial_balance_0').val();

            for (let index = 0; index < 4; index++) {
                let invoiceId = $('#create_invoice_id_'+index);
                let initialBalance = $('#create_initial_balance_'+index);
                let monthlyPayment = $('#create_monthly_payment_'+index);
                let status = $('#create_status_'+index);
                let creditStartAt = $('#create_credit_start_at_'+index);
                let creditPaymentAt = $('#create_credit_payment_at_'+index);

                if (index < quotasQty) {
                    invoiceId.removeAttr('disabled');
                    initialBalance.removeAttr('disabled');
                    monthlyPayment.removeAttr('disabled');
                    status.removeAttr('disabled');
                    creditStartAt.removeAttr('disabled');
                    creditPaymentAt.removeAttr('disabled');
                }else{
                    invoiceId.attr('disabled', 'disabled');
                    initialBalance.attr('disabled', 'disabled');
                    monthlyPayment.attr('disabled', 'disabled');
                    status.attr('disabled', 'disabled');
                    creditStartAt.attr('disabled', 'disabled');
                    creditPaymentAt.attr('disabled', 'disabled');

                    invoiceId.val('');
                    initialBalance.val('');
                    monthlyPayment.val('');
                    status.val('0');
                    creditStartAt.val('');
                    creditPaymentAt.val('');
                }

                if (index >= quotasQty) {
                    continue;
                }

                let afterIndex = index + 1;

                if (initialBalanceVal > 0 && quotasQty > 0) {
                    var monthlyPaymentVal = initialBalanceVal / quotasQty;
                    var monthlyPaymentValCeil = Math.ceil(monthlyPaymentVal * 100) / 100;
                    $('#create_monthly_payment_'+index).val(monthlyPaymentValCeil.toFixed(2));

                    var initialBalanceAfter = initialBalance.val() - monthlyPaymentVal;
                    $('#create_initial_balance_'+afterIndex).val(initialBalanceAfter.toFixed(2));
                }

                let today = new Date(creditStartAt.val());

                var paymentDate = new Date(today.getTime() + daysAdd);

                var day = paymentDate.getDate();
                var month = paymentDate.getMonth() + 1;
                var year = paymentDate.getFullYear();

                var formattedDate = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);

                creditPaymentAt.val(formattedDate);
                $('#create_credit_start_at_'+afterIndex).val(formattedDate);
            }
        }

        function editQuota(params) {
            let parts = params.split('*-*');
            let id = parts[0];
            let invoice_ids = parts[1].split(',');
            let credit_start_at = parts[2];
            let credit_payment_at = parts[3];
            let initial_balance = parts[4];
            let monthly_payment = parts[5];
            let status = parts[6];

            let url = "{{route('special_negotiations.quota.update', ':id')}}".replace(':id', id);

            $('#editQuotaForm').attr('action', url);

            $('#edit_invoice_id').val(invoice_ids);
            $('#edit_credit_start_at').val(credit_start_at);
            $('#edit_credit_payment_at').val(credit_payment_at);
            $('#edit_initial_balance').val(initial_balance);
            $('#edit_monthly_payment').val(monthly_payment);
            $('#edit_status').val(status);

            $('#editQuotaModal').modal('show');
        }

        // Payments
        function addPayment(params) {
            let parts = params.split('*-*');
            let id = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));
            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));

            $('#create_quota_id').val(id);

            $('#create_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                $('#create_invoice_id').append(options);
            })
            $('#create_invoice_id').trigger('change');
            $('#createPaymentModal').modal('show');
        }

        $('#create_invoice_id').change(function() {
            let invoice_id = $('#create_invoice_id').val();
            if (invoice_id) {
                let url = "{{route('special_negotiations.get_payments', ':id')}}".replace(':id', invoice_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#create_payment_id').empty();
                        data.payments.forEach(element => {
                            let options = '<option value="' + element.id + '">' + element.amount + ' - ' + element.payment_date + '</option>';
                            $('#create_payment_id').append(options);
                        })
                        $('#create_payment_id').trigger('change');
                    }
                })
            }
        })

        $('#create_payment_id').change(function() {
            const optionSelectedPaymentId = $(this).find('option:selected');
            const textSelectedPaymentId = optionSelectedPaymentId.text();
            console.log(textSelectedPaymentId);
            let parts = textSelectedPaymentId.split(' - ');
            let mount = parseFloat(parts[0]);
            let date = parts[1];

            $('#create_mount_balance').val(mount);
            $('#create_payment_at').val(date);
        })

        function editPayment(params) {
            let parts = params.split('*-*');
            let quotaId = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));

            let paymentId = parts[2];
            let mountBalance = parts[3];
            let createPaymentAt = parts[4];
            let invoiceId = parts[5];

            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));

            let url = "{{route('special_negotiations.payment.update', ':id')}}".replace(':id', paymentId);

            $('#editPaymentForm').attr('action', url);

            $('#edit_quota_id').val(quotaId);

            $('#edit_quota_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '';
                if (element.id == invoiceId) {
                    options = '<option value="' + element.id + '" selected>' + element.invoice_number + '</option>';
                }else{
                    options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                }
                $('#edit_quota_invoice_id').append(options);
            })
            $('#edit_quota_invoice_id').trigger('change');
            $('#edit_mount_balance').val(mountBalance);
            $('#edit_payment_at').val(createPaymentAt);
            $('#edit_payment_id').val(paymentId);
            $('#editPaymentModal').modal('show');
        }

        $('#edit_quota_invoice_id').change(function() {
            let invoice_id = $('#edit_quota_invoice_id').val();
            if (invoice_id) {
                let url = "{{route('special_negotiations.get_payments', ':id')}}".replace(':id', invoice_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        $('#edit_payment_id').empty();
                        data.payments.forEach(element => {
                            let options = '<option value="' + element.id + '">' + element.amount + ' - ' + element.payment_date + '</option>';
                            $('#edit_payment_id').append(options);
                        })
                        $('#edit_payment_id').trigger('change');
                    }
                })
            }
        })

        $('#edit_payment_id').change(function() {
            const optionSelectedPaymentId = $(this).find('option:selected');
            const textSelectedPaymentId = optionSelectedPaymentId.text();
            console.log(textSelectedPaymentId);
            let parts = textSelectedPaymentId.split(' - ');
            let mount = parseFloat(parts[0]);
            let date = parts[1];

            $('#edit_mount_balance').val(mount);
            $('#edit_payment_at').val(date);
        })

        // Refunds

        function addRefund(params) {
            let parts = params.split('*-*');
            let id = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));
            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));

            $('#create_refund_quota_id').val(id);

            $('#create_refund_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                $('#create_refund_invoice_id').append(options);
            })
            $('#create_refund_invoice_id').trigger('change');
            $('#createRefundModal').modal('show');
        }

        $('#create_refund_invoice_id').change(function() {
            let invoice_id = $('#create_refund_invoice_id').val();
            if (invoice_id) {
                let url = "{{route('special_negotiations.get_refunds', ':id')}}".replace(':id', invoice_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#create_refund_id').empty();
                        data.refunds.forEach(element => {
                            let options = '<option value="' + element.id + '">' + element.total_refunded + ' - ' + element.refund_date + ' - ' + element.refund_number + '</option>';
                            $('#create_refund_id').append(options);
                        })
                        $('#create_refund_id').trigger('change');
                    }
                })
            }
        })

        $('#create_refund_id').change(function() {
            const optionSelectedRefundId = $(this).find('option:selected');
            const textSelectedRefundId = optionSelectedRefundId.text();
            console.log(textSelectedRefundId);
            let parts = textSelectedRefundId.split(' - ');
            let mount = parseFloat(parts[0]);
            let date = parts[1];

            $('#create_refund_mount_balance').val(mount);
            $('#create_refund_at').val(date);
        })

        function editRefund(params) {
            let parts = params.split('*-*');
            let quotaId = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));

            let refundId = parts[2];
            let mountBalance = parts[3];
            let createRefundAt = parts[4];
            let invoiceId = parts[5];

            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));

            let url = "{{route('special_negotiations.refund.update', ':id')}}".replace(':id', refundId);

            $('#editRefundForm').attr('action', url);

            $('#edit_refund_quota_id').val(quotaId);

            $('#edit_refund_quota_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '';
                if (element.id == invoiceId) {
                    options = '<option value="' + element.id + '" selected>' + element.invoice_number + '</option>';
                }else{
                    options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                }
                $('#edit_refund_quota_invoice_id').append(options);
            })
            $('#edit_refund_quota_invoice_id').trigger('change');
            $('#edit_refund_mount_balance').val(mountBalance);
            $('#edit_refund_at').val(createRefundAt);
            $('#edit_refund_id').val(refundId);
            $('#editRefundModal').modal('show');
        }

        $('#edit_refund_quota_invoice_id').change(function() {
            let invoice_id = $('#edit_refund_quota_invoice_id').val();
            if (invoice_id) {
                let url = "{{route('special_negotiations.get_refunds', ':id')}}".replace(':id', invoice_id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
                        $('#edit_refund_id').empty();
                        data.refunds.forEach(element => {
                            let options = '<option value="' + element.id + '">' + element.total_refunded + ' - ' + element.refund_date + ' - ' + element.refund_number + '</option>';
                            $('#edit_refund_id').append(options);
                        })
                        $('#edit_refund_id').trigger('change');
                    }
                })
            }
        })

        $('#edit_refund_id').change(function() {
            const optionSelectedRefundId = $(this).find('option:selected');
            const textSelectedRefundId = optionSelectedRefundId.text();
            console.log(textSelectedRefundId);
            let parts = textSelectedRefundId.split(' - ');
            let mount = parseFloat(parts[0]);
            let date = parts[1];

            $('#edit_refund_mount_balance').val(mount);
            $('#edit_refund_at').val(date);
        })

        // Discounts

        function addDiscount(params) {
            let parts = params.split('*-*');
            let id = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));
            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));
            let total_amount = parts[2];

            $('#create_discount_total_amount').val(total_amount);

            $('#create_discount_quota_id').val(id);

            $('#create_discount_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                $('#create_discount_invoice_id').append(options);
            })
            $('#create_discount_invoice_id').trigger('change');
            $('#createDiscountModal').modal('show');
        }

        $('#create_discount_invoice_id').change(function() {
            let invoice_id = $('#create_discount_invoice_id').val();
            if (invoice_id) {
                invoicesFilter = invoices.filter(objeto => objeto.id == invoice_id);
                amount = invoicesFilter[0].amount;
                $('#create_discount_amount_invoice').val(amount);
            }
            $('#create_porcent_quotas_discount').trigger('change');
        })

        $('#create_porcent_quotas_discount').change(function() {
            changeCreatePorcentQuotasDiscount(this);
        })
        $('#create_porcent_quotas_discount').keyup(function() {
            changeCreatePorcentQuotasDiscount(this);
        })

        function changeCreatePorcentQuotasDiscount(event){
            let value = event.value;
            let total_amount = $('#create_discount_total_amount').val();

            let porcent = (value / 100) * total_amount;
            porcent = porcent.toFixed(2);
            let final_amount = total_amount - porcent;
            final_amount = final_amount.toFixed(2);

            $('#create_discount_applied').val(porcent);
            $('#create_discount_total_amount_sub_porcent').val(final_amount);

            let amount_invoice = $('#create_discount_amount_invoice').val();

            let final_amount_invoice = amount_invoice - porcent;
            final_amount_invoice = final_amount_invoice.toFixed(2);

            $('#create_discount_amount_invoice_sub_porcent').val(final_amount_invoice);
        }

        function editDiscount(params) {
            let parts = params.split('*-*');
            let quota_id = parts[0];
            let invoice_ids = parts[1].split(',');
            invoice_ids = invoice_ids.map(id => parseInt(id));
            const invoicesFilter = invoices.filter(objeto => invoice_ids.includes(objeto.id));
            let total_amount = parts[2];

            let discount_id = parts[3];
            let porcent_quotas_discount = parts[4];
            let invoice_id = parts[5];

            $('#edit_discount_total_amount').val(total_amount);

            $('#edit_discount_quota_id').val(quota_id); //<--

            let url = "{{route('special_negotiations.discount.update', ':id')}}".replace(':id', discount_id);
            $('#editDiscountForm').attr('action', url);

            $('#edit_discount_invoice_id').empty();
            invoicesFilter.forEach(element => {
                let options = '<option value="' + element.id + '">' + element.invoice_number + '</option>';
                $('#edit_discount_invoice_id').append(options);
            })

            $('#edit_discount_invoice_id').val(invoice_id);

            $('#edit_discount_invoice_id').trigger('change');

            $('#edit_porcent_quotas_discount').val(porcent_quotas_discount);

            $('#editDiscountModal').modal('show');
        }

        $('#edit_discount_invoice_id').change(function() {
            let invoice_id = $('#edit_discount_invoice_id').val();
            if (invoice_id) {
                invoicesFilter = invoices.filter(objeto => objeto.id == invoice_id);
                amount = invoicesFilter[0].amount;
                $('#edit_discount_amount_invoice').val(amount);
            }
            $('#edit_porcent_quotas_discount').trigger('change');
        })

        $('#edit_porcent_quotas_discount').change(function() {
            changeEditPorcentQuotasDiscount(this);
        })
        $('#edit_porcent_quotas_discount').keyup(function() {
            changeEditPorcentQuotasDiscount(this);
        })

        function changeEditPorcentQuotasDiscount(event){
            let value = event.value;
            let total_amount = $('#edit_discount_total_amount').val();

            let porcent = (value / 100) * total_amount;
            porcent = porcent.toFixed(2);
            let final_amount = total_amount - porcent;
            final_amount = final_amount.toFixed(2);

            $('#edit_discount_applied').val(porcent);
            $('#edit_discount_total_amount_sub_porcent').val(final_amount);

            let amount_invoice = $('#edit_discount_amount_invoice').val();

            let final_amount_invoice = amount_invoice - porcent;
            final_amount_invoice = final_amount_invoice.toFixed(2);

            $('#edit_discount_amount_invoice_sub_porcent').val(final_amount_invoice);
        }

        // Tracking

        async function showTracking(current_model, id) {
            let data = {
                    current_model : current_model,
                    id : id
                };
            Livewire.dispatch('reload-data-tracking-table', data);
            $('#trackingModal').modal('show');
        }
    </script>
@stop
