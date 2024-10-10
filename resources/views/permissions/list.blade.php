@extends("adminlte::page")

@section("title", "index")

@section("content_header")
<div class="container ">
    <div class="d-flex justify-content-between">
        <div class="header">
            <h1>
                Listado de Permisos
            </h1>
        </div>
        <div class="">
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">Nuevo</a>
        </div>

    </div>
</div>
@stop

@section("content")
<form action=""></form>
<div class="container">
    @foreach($categories as $key => $category)
    <div class="card p-5">
    <h5>Permisos: {{$category}}</h5>
    <livewire:Datatables.permissions-table :category="$key" :categories="$categories" />
    <hr class="my-3">
    </div>
   
    @endforeach
</div>
@stop

@section("css")
{{-- Add here extra stylesheets --}}
@stop

@section("js")
{{-- Add here extra javascript --}}
@stop