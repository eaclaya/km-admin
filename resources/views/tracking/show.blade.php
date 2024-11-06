@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Exportar Facturas</h1>
@stop

@section("content")
    <div class="container">
        <hr>
        <livewire:Datatables.tracking-table :current_model="$model" :id="$id" />
        <hr>
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
