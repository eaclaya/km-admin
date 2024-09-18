@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.sales_by_sac') }}">
                <div class="col-md-3">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php  echo date('Y-m-d') ?>"/>
                </div>
                <div class="col-md-3">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php  echo date('Y-m-d') ?>" />
                </div>
                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <select name="export" class="select-group control-form form-control">
                        <option value="0">Ver Resultados</option>
                        <option value="1">Exportar</option>
                    </select>
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
                            <td>Auxiliar SAC</td>
                            <td>Cliente</td>
                            <td>Telefono</td>
                            <td>Tienda</td>
                            <td>Direccion</td>
                            <td>Vendedor</td>
                            <td>Factura</td>
                            <td>Fecha Factura</td>
                            <td>Costo</td>
                            <td>Total contado</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item['employee_sac'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['phone'] }}</td>
                                <td>{{ $item['account_name'] }}</td>
                                <td>{{ $item['address'] }}</td>
                                <td>{{ $item['employee'] }}</td>
                                <td>{{ $item['invoice'] }}</td>
                                <td>{{ $item['invoice_date'] }}</td>
                                <td>{{ $item['total_cost'] }}</td>
                                <td>{{ $item['total'] }}</td>
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
    </script>
@stop
