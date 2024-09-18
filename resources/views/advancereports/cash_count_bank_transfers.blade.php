@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.bank_transfers_cash_count') }}">
                <div class="col-md-2">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-md-2">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-md-2">
                    <p>Cuentas Bancarias</p>
                    <select name="finance_id" id="finance_id" class="control-form form-control">
                        <option value="">Todas</option>
                        @foreach ($financeAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <p>Tipos De Pagos</p>
                    <select name="pay_id" id="pay_id" class="control-form form-control">
                        <option value="">Todas</option>
                        @foreach ($paymentTypes as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <p>&nbsp;</p>
                    <select name="export" class="control-form form-control">
                        <option value="0">Ver Resultados</option>
                        <option value="1">Exportar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
            </form>
        </div>
    </div>
    <hr>

    @if (isset($result))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
						    <td>Hecho por</td>
						    <td>Tienda</td>
                            <td>Tipo</td>
                            <td>Numero</td>
                            <td>Fecha</td>
                            <td>Cuenta Origen</td>
                            <td>Cuenta Destino</td>
                            <td>Usuario</td>
                            <td>Estado</td>
                            <td>Descripción</td>
                            <td>ID from Cierre</td>
                            <td>ID to Cierre</td>
                            <td>Monto</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
							    <td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td> @if(strpos($item->number, 'MT-') !== false)
                                        Transferencia
                                        @elseif(strpos($item->number, 'I-0') !== false)
                                        Otro Ingreso 
                                        @elseif(isset($item->store))
                                        Credito Adelanto 
                                        @else
                                        Pago Factura  
                                        @endif
                                </td>
                                <td>{{ $item->number }}</td>
                                <td>{{ $item->created_at}}</td>
                                <td>{{ isset($item->fa_name)? $item->fa_name : '' }}</td>
                                <td>{{ str_replace('_',' ',$item->fat_name)   }}</td>

                                <td>{{ $item->user_name .' '.$item->user_name2 }}</td>
                                <td>
                                        @if ($item->completed)
                                            Completada
                                        @else
                                            Pendiente
                                        @endif
                                </td>
                                <td>{{ isset($item->description)? $item->description : '' }}</td> 
                                <td>{{ $item->cash_count_id }}</td>
                                <td>{{ isset($item->cash_count_out_id)?$item->cash_count_out_id:'' }}</td>
                                <td>{{ $item->amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });
        });
        $("#pay_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#finance_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
