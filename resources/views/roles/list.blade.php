@extends("adminlte::page")

@section("title", "index")

@section("content_header")
<div class="container ">
    <div class="d-flex justify-content-between">
        <div class="header">
            <h1>
                Listado de Roles
            </h1>
        </div>
        <div class="">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Nuevo</a>
        </div>

    </div>
</div>

@stop

@section("content")
<form action=""></form>
<div class="container">
    <div class="card p-3">
        <div class="card-body">
            <livewire:Datatables.roles-table />
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