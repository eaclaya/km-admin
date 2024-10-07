@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.incomes_cash_count') }}">
                <div class="col-md-2">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-md-2">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>

                <div class="col-md-2">
                    <p>Tienda</p>
                    <select name="store" id="store" class="control-form form-control">
                        <option value="0">Todas</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
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
                            <td>Creada</td>
                            <td>Asignada A Cierre</td>
                            <td>Tienda</td>
                            <td>Categoria</td>
                            <td>Numero</td>
                            <td>Tipo Pago</td>
                            <td>Usuario</td>
                            <td>ID Cierre</td>
                            <td>Descripcion</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
							    <td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->asig_date }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>{{ $item->ca_name }}</td>
                                <td>{{ $item->number }}</td>
                                <td>{{ substr($item->py_name, 0, 20) }}</td>
                                <td>{{ $item->user_name .' '.$item->user_name2 }}</td>
                                <td>{{ $item->cash_count_id }}</td>
                                <td>{{ $item->description }}</td>
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
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop