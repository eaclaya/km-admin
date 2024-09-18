@extends('adminlte::page')

@section('css')

@stop

@section('content_header')
    <h1>
        Procesos de Libro Diario
    </h1>
@stop

@section('content')
    <div class="container">
        <div class="container">
            <form class="filter-form row" method="POST" action="{{route('finance_daybook.process') }}">
                @csrf
                <div class="col-md-3">
                    <p>Fecha</p>
                    <input type="date" class="form-control" name="date" />
                </div>
                <div class="col-md-3">
                    <p>Tiendas</p>
                    @livewire('components.select2-model-component', $bodySelectAccount)
                </div>
                <div class="col-md-3">
                    <p>Tipo</p>
                    @livewire('components.select2-array-component', $bodySelectType)
                </div>
                <div class="col-md-3">
                    <p>Procesar</p>
                    <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
            </form>
            <hr>
            <livewire:Datatables.report-process-table :name="$name" />
        </div>
    </div>
@stop

@section('js')
@stop
