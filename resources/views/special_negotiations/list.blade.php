@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>
        Listado
        @if(isset($notIsCompleted))
            <a class="btn btn-success" href="{{route('clone_models.list',['model'=> $model])}}">Ver Todos los Procesos</a>
        @else
            <a class="btn btn-success" href="{{route('clone_models.list',['model'=> $model, 'not_is_completed' => 1])}}">Ver Solo Procesos No Completados</a>
        @endif
    </h1>
@stop

@section("content")
    <div class="container">
        <livewire:Datatables.clone-models-table :model="$model" :account_id="$account_id" :notIsCompleted="$notIsCompleted" />
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
