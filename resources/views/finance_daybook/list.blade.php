@extends('adminlte::page')

@section('css')

@stop

@section('content_header')
    <h1>
        Libro Diario -
        <a class="btn btn-primary btn-sm" href="{{route('finance_daybook.process') }}"> Procesos </a>
        <a class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModalCreated"> Crear </a>
    </h1>
@stop

@section('content')
    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @livewire('Datatables.daybook-table',['type' => $type,'id' => $id,])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Asiento Diario</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @livewire('Components.Daybook.daybook-entry-view-component')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalCreated" style="overflow: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalCreatedLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalCreatedLabel">Crear Asiento Diario</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" style="overflow-x: auto;">
                    @livewire('Components.Daybook.form-created-daybook-component')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
