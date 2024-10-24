@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Descuento en Facturas -
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">
            Procesos
        </button>
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <livewire:Datatables.products-table />
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    <script>
    </script>
@stop
