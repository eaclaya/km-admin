@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Descuento en Facturas</h1>
@stop

@section("content")
    <div class="container">
        <form action="{{route('invoice_discount.set_discount')}}" method="post" enctype="multipart/form-data">
            <div class="input-group">
                @csrf
                <label for="formFileSm" class="input-group-text form-label">Subir Archivo de facturas Modificadas</label>
                <input class="form-control" id="formFileSm" type="file" name="csv_file">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Subir</button>
            </div>
        </form>
        <br>
        <div class="container">
            <livewire:Datatables.invoice-discount-table :account_id="$account_id"/>
        </div>
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
