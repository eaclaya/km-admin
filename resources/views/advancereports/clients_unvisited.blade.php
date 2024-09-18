@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.clients_unvisited') }}">
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
                            <td>ID</td>
                            <td>Tienda</td>
                            <td>Ruta</td>
                            <td>Cliente</td>
                            <td>Empresa</td>
                            <td>Tipo</td>
                            <td>Telefono</td>
                            <td>Direccion</td>
                            <td>Dia Visita</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $item['account_name'] }}</td>
                                <td>{{ $item['route_name'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['company_name'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['phone'] }}</td>
                                <td>{{ $item['address1'] }}</td>
                                <td>{{ $item['frequency_day'] }}</td>
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
                    [0, "asc"]
                ]
            });
        });
    </script>
@stop
