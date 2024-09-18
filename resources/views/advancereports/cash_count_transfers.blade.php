@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.transfers_cash_count') }}">
                <div class="col-md-2">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-md-2">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-md-2">
                    <p>Cuentas De Origen</p>
                    <select name="from_finance_id" id="from_finance_id" class="control-form form-control">
                        <option value="">Todas</option>
                        @foreach ($financeAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <p>Cuentas De Destino</p>
                    <select name="to_finance_id" id="to_finance_id" class="control-form form-control">
                        <option value="">Todas</option>
                        @foreach ($financeAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
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
                            <td>Transaccion</td>
                            <td>Fecha</td>
                            <td>Tienda</td>
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
                                <td>{{ $item->document_number }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>{{ $item->fa_name }}</td>
                                <td>{{ $item->fat_name }}</td>

                                <td>{{ $item->user_name .' '.$item->user_name2 }}</td>
                                <td>@if ($item->completed)
                                    Completada
                                @else
                                    Pendiente
                                @endif</td>
                                <td>{{ $item->description }}</td> 
                                <td>{{ $item->cash_count_id }}</td>
                                <td>{{ $item->cash_count_out_id }}</td>
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
        $("#from_finance_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#to_finance_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
