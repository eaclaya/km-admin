@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.vouchers_discount') }}">
                <p>Exportar Clientes Con Vales</p>
                <div class="col-md-2">
                    <select name="expoor_clients" class="control-form form-control">
                        <option value="1">Si</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block">EXPORTAR</button>
                </div>
            </form>
        </div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.vouchers_discount') }}">
                <p>Ver o Exportar Descuentos De Vales</p>
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
                    <select id="store" name="store" class="control-form form-control">
                        <option value="0">Todas</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
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
                            <td>Tienda</td>
                            <td>Factura ID</td>
                            <td>Factura</td>
                            <td>Cliente ID</td>
                            <td>Cliente</td>
                            <td>Fecha</td>
                            <td>Aprobó</td>
                            <td>Ventas KMS</td>
                            <td>%</td>
                            <td>Descuento</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->accounts_name }}</td>
                                <td>{{ $item->invoice_id }}</td>
                                <td>{{ $item->invoice_number }}</td>
                                <td>{{ $item->client_id }}</td>
                                <td>{{ $item->clients_name }}</td>
                                <td>{{ $item->date_vouchers }}</td>
                                <td>{{ $item->accepted_by }}</td>
                                <td>{{ $item->invoice_kms_amount }}</td>
                                <td>{{ $item->percentage_amount }}</td>
                                <td>{{ $item->vouchers_amount }}</td>
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
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
