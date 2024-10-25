@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Listado de Negociaciones - <a href="{{route('special_negotiations.create')}}">Crear</a>
    </h1>
@stop

@section("content")
    <div class="container">
        <livewire:Datatables.special-negotiations-table />
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
