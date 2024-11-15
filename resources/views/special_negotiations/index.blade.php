@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Listado de Negociaciones - <a class="btn btn-success" href="{{route('special_negotiations.create')}}">Crear</a>
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @isset($routes_id)
                    <livewire:Datatables.special-negotiations-table :routes_id='$routes_id' />
                @else
                    No Tiene Permitido Visualizar.
                @endisset
            </div>
        </div>
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
