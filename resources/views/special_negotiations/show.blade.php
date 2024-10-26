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
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal">
                    Crear Cuota
                </button>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>N.º de Pago</th>
                            <th>N.º de Factura</th>
                            <th>Fecha de Inicio de Crédito</th>
                            <th>Fecha de Pago s/Condiciones</th>
                            <th>Dias Transcurridos</th>
                            <th>Inicio Saldo</th>
                            <th>Pago Mensual Sin Descuento</th>
                            <th>Estado de la Couta</th>
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
                            <tr>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                                <td>aqui vamos</td>
                            <tr>
                        @empty

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row">
                        <div class="col-md-3">
                            <label for="invoice_id" class="form-label">Factura:</label>
                            <select name="invoice_id" id="invoice_id" class="form-control">
                                @foreach ($special_negotiation->invoices as $invoice)
                                    <option value="{{$invoice->id}}">{{$invoice->invoice_number}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="initial_balance" class="form-label">Saldo inicial:</label>
                            <input type="number" class="form-control" id="initial_balance" name="initial_balance">
                        </div>

                        <div class="col-md-3">
                            <label for="monthly_payment" class="form-label">Pago mensual:</label>
                            <input type="number" class="form-control" id="monthly_payment" name="monthly_payment">
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Estado:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="0">Activo</option>
                                <option value="1">Pagado</option>
                                <option value="2">Vencido</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="credit_start_at" class="form-label">Fecha de inicio de crédito:</label>
                            <input type="date" class="form-control" id="credit_start_at" name="credit_start_at">
                        </div>

                        <div class="col-md-3">
                            <label for="credit_payment_at" class="form-label">Fecha de pago de crédito:</label>
                            <input type="date" class="form-control" id="credit_payment_at" name="credit_payment_at">
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
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    <script>

    </script>
@stop
