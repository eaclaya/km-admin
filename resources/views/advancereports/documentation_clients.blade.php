@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-8">
            <form class="filter-form" method="POST" action="{{ route('advancereports.documentation_clients') }}">
                
                <div class="col-md-4">
                    <p>Rutas</p>
                    <select name="route_id" class="select-group control-form form-control">
                        <option value="">Todos</option>
                        @foreach($routes as $route)
                            <option value="{{$route->id}}">{{$route->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <select name="export" class="select-group control-form form-control">
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
    @if (isset($results))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Ruta</td>
                            <td>Cliente</td>
                            <td>Empresa</td>
                            <td>Tipo</td>
                            <td>Telefono</td>
                            <td>Creado</td>
                            <td>Documentos</td>
                            <td>rtn</td> 
                            <td>croquis</td>
                            <td>identidad</td> 
                            <td>letra_cambio_firmada</td>
                            <td>foto_actividad_negocio</td> 
                            <td>recibo_servicio_publico</td> 
                            <td>revision_historial</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $item['route_name'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['company_name'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['phone'] }}</td>
                                <td>{{ $item['created_at'] }}</td>
                                <td>{{ count($item['extra_attributes']['adjunts'] ?? []) }} de 7</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['rtn'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['rtn'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['croquis'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['croquis'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['identidad'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['identidad'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['letra_cambio_firmada'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['letra_cambio_firmada'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['foto_actividad_negocio'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['foto_actividad_negocio'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['recibo_servicio_publico'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['recibo_servicio_publico'] }}" target="_blank"> Ver </a> @endif</td>
                                <td> @if(isset($item['extra_attributes']['adjunts']['revision_historial_credito'])) <a href="{{config('app.url') }}/{{$item['extra_attributes']['adjunts']['revision_historial_credito'] }}" target="_blank"> Ver </a> @endif</td>
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
                    [2, "asc"]
                ]
            });
        });
    </script>
@stop