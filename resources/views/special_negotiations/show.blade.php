@extends("adminlte::page")

@section("title", "Editar")

@section("content_header")
    <h1>
        Negociacion Especial
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start">
            <div class="card col-md-6">
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
                                        {{$invoice->amount}}
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
                        0% (pendiente)
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Condicion de Credito</label>
                        30/60 (pendiente)
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
                        L 0,00 (pendiente)
                    </div>
                    <div class="border-bottom list-group-item-secondary">
                        <label class="control-label col-5">TOTAL CREDITO+REVISION HISTORIAL</label>
                        {{$special_negotiation->amount}} + (pendiente)
                    </div>
                    <div class="border-bottom bg-light">
                        <label class="control-label col-5">Pago mensual sin descuento otorgado</label>
                        (pendiente)
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Numero de Pagos</label>
                        (pendiente)
                    </div>
                    <div class="border-bottom list-group-item-secondary">
                        <label class="control-label col-5">Descuento total otorgado</label>
                        (pendiente)
                    </div>
                    <div class="border-bottom">
                        <label class="control-label col-5">Descuento por pago otorgado</label>
                        (pendiente)
                    </div>
                    <div class="border-bottom bg-light">
                        <label class="control-label col-5">Importe total del crédito - descuento</label>
                        {{$special_negotiation->amount}} - (pendiente)
                    </div>
                    <div class="border-bottom  list-group-item-secondary">
                        <label class="control-label col-5">Pago mensual con descuento otorgado</label>
                        {{$special_negotiation->amount}} - (pendiente)
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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
                            L 0,00 (pendiente)
                        </div>
                        <div class="border-bottom  bg-light">
                            <label class="control-label col-5">Revision Record Créditicio</label>
                            L 0,00 (pendiente)
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
                <table class="table text-center">
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
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
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
                                    <td>-</td>
                                    <td>
                                        {{$loop->iteration}}
                                        <a class="btn btn-primary btn-sm"
                                            onclick="editPayment('{{$quota->id}}*-*{{$quota->invoices->pluck('id')->implode(',')}}*-*{{$payment->id}}*-*{{$payment->mount_balance}}*-*{{$payment->create_payment_at}}*-*{{$payment->invoice_id}}')">
                                            Editar
                                        </a>
                                    </td>
                                    <td>{{$payment->payment_at}}</td>
                                    <td>{{$payment->mount_balance}}</td>
                                    <td>{{$payment->mount_balance_total}}</td>
                                    <td></td>
                                    <td>{{$payment->overdue_balance}}</td>
                                    <td>{{$payment->final_balance}}</td>
                                </tr>
                            @empty
                                Sin Pagos
                            @endforelse
                        @empty
                            Cuotas por Agregar
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                            <div class="col-md-3">
                                <label for="edit_quota_invoice_id" class="form-label">Factura:</label>
                                <select name="invoice_id" id="edit_quota_invoice_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="edit_payment_id" class="form-label">Pagos:</label>
                                <select name="payment_id" id="edit_payment_id" class="form-control" required>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="edit_mount_balance" class="form-label">Monto Abonado:</label>
                                <input
                                    type="number" class="form-control" id="edit_mount_balance"
                                    name="mount_balance" step="0.01"
                                    required
                                />
                            </div>
                            <div class="col-md-3">
                                <label for="edit_payment_at" class="form-label">Fecha de pago:</label>
                                <input type="date" class="form-control" id="edit_payment_at" name="payment_at" required />
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
        let invoices = {!! json_encode($special_negotiation->invoices) !!};

        $(document).ready(function() {
            conditionChange();
        });

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
    </script>
@stop
