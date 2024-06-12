@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Listado de Permisos
    </h1>
@stop

@section("content")
    <form action=""></form>
    <div class="container">
        @foreach($categories as $key => $category)
            <h2>Permisos: {{$category}}</h2>
            <livewire:Datatables.permissions-table :category="$key" :categories="$categories" />
            <hr class="my-3">
        @endforeach
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
