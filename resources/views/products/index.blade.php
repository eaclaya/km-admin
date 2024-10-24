@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Productos
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
