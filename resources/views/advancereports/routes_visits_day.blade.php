@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.routes_visits_day') }}">
                <div class="col-md-3">
                    <p>Dia De Visita</p>
                    <input type="date" class="form-control" name="date" value="<?php  echo date('Y-m-d') ?>"/>
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
                            <td>Ruta</td>
                            <td>Usuario</td>
                            <td>Clientes</td>
                            <td>Visitas</td>
                            <td>Visitas de otro dia</td>
                            <td>Fecha</td>
                            <td>Porcentaje</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td><strong>{{ $item['id'] }}</strong></td>
                                <td><strong>{{ $item['name'] }}</strong></td>
                                <td><strong>{{ $item['user'] }}</strong></td>
                                <td><strong>{{ $item['clients'] }}</strong></td>
                                <td><strong>{{ $item['visits'] }}</strong></td>
                                <td><strong>{{ $item['other_visits'] }}</strong></td>
                                <td><strong>{{ $item['fecha'] }}</strong></td>
                                <td><strong>{{ $item['percentage'] }}</strong></td>
                            </tr>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>Nombre</td>
                                <td>Compañia</td>
                                <td>Telefono</td>
                                <td>Resultado visita</td>
                                <td>Fecha y hora</td>
                            </tr>
                            @foreach ($item['clients_today']??[] as $key =>  $i)
                                <tr>
                                    <td></td>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $i['name'] }}</td>
                                    <td>{{ $i['company_name'] }}</td>
                                    <td>{{ $i['phone'] }}</td>
                                    <td>{{ $i['result_visit'] }}</td>
                                    <td>{{ $i['created_at'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
