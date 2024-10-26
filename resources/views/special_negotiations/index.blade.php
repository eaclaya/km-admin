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
                <livewire:Datatables.special-negotiations-table />
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
