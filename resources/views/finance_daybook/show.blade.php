@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop

@section('content')
    @parent
    @if (Session::has('message'))
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
    <div class="evaluationProcess">
        <div class="col-xs-4">
            <a data-toggle="modal" data-target="#myModalCreateEvaluationProcess" class="btn btn-sm btn-success">Nuevo proceso de Evaluacion</a>
		</div>
        <div class="col-xs-4">
            @if(Auth::user()->realUser()->hasAnyRole(['Nivl III', 'Auditoria', 'Usuario especial', 'Auxiliar RRHH/capacitaciones', 'Recursos Humanos', 'Capacitacion Manuales y Procesos']))
                {{-- <a class="btn btn-primary" style="float: right" href="{{ route('evaluationprocess.reports') }}">Reporte de evaluationProces</a> --}}
                <a class="btn btn-success" style="float: right" href="{{ route('evaluationprocess.fields') }}">Gestionar los Campos</a>
            @endif
		</div>
        @if(isset($evaluationProcess))
            <table class="table">
                <thead>
                    <tr>
                        <td>Numero</td>
                        <td>Ciclo</td>
                        <td>Tipo</td>
                        <td>Entidad Evaluada</td>
                        <td>Notas</td>
                        <td>Fecha</td>
                        <td>Acciones</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluationProcess as $item)
                    <tr>
                        <td><a href="{{ route('evaluationprocess.edit', $item->id) }}">{{$item->id}}</a></td>
                        <td>{{$item->cycle}}</td>
                        <td>{{$item->getProcessType()}}</td>
                        <td>
                            @php
                                $model_name = '';
                                switch ($item->evaluation_process_type) {
                                    case 0:
                                        $employee = $item->employee;
                                        $model_name = $employee->first_name . ' ' . $employee->last_name;
                                        break;

                                    case 1:
                                        $model_name = $item->account->name;
                                        break;

                                    case 2:
                                        $model_name = $item->zone->name;
                                        break;

                                    case 3:
                                        $model_name = 'Supervisores - Todas las Zonas';
                                        break;

                                    default:
                                        $model_name = '';
                                        break;
                                }
                            @endphp
                            {{$model_name}}
                        </td>
                        <td>{{$item->notes}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>
                        @if(Auth::user()->realUser()->hasAnyRole(['Nivl III', 'Auditoria', 'Usuario especial']))
                        <a href="{{ route('evaluationprocess.delete', $item->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-center"><h2 class="text-center">Ver Mas Procesos de Evaluacion:</h2> 
                <div>{{ $evaluationProcess->links() }}</div>
            </div>
        @else
            <br>
            <h2>No hay registros</h2>
        @endif
        <div class="modal fade" id="myModalCreateEvaluationProcess" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 >Crear Proceso de Evaluacion</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('evaluationprocess.create')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row text-center">
                                <div class="col-md-6">
                                    Seleccionar el Tipo de Evaluación
                                    <select id="processTypes" name='evaluation_process_type' required>
                                        <option value=""></option>
                                        @foreach ($processTypes as $processType_id => $type)
                                            <option value="{{$processType_id}}">{{trans("texts.$type")}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    Seleccionar el Ciclo de Evaluación
                                    <select id="cycle" name='cycle' required>
                                        <option value=""></option>
                                        @foreach ($cycles as $cycle)
                                            <option value="{{$cycle}}">{{$cycle}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    Seleccionar Zona
                                    <select class='dinamic_select' id="zones_select" name='evaluation_zone_id' disabled>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    Seleccionar Tienda
                                    <select class='dinamic_select' id="account_select" name='evaluation_account_id' disabled>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    Seleccionar Empleado
                                    <select class='dinamic_select' id="employee_select" name='evaluation_employee_id' disabled>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin: 50px auto;display: block">Guardar</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="close" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="{{asset('js/jquery.dataTables.min.js') }}"></script>
    <script>
        @if(isset($evaluationProcess))
            $(document).ready( function () {
                $('table.table').DataTable({
                    "order": [[ 0, "desc" ]],
                });
            } );
        @endif
        $processTypes = {!! collect($processTypes) !!};
        $('#zones_select').select2({
            ajax: {
                url: '{{ route("kpis.zones.search") }}',
                dataType: 'json',
                type: 'GET',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page
                    }
                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    console.dir(params.page);
                    let data_results = [];
                    let arr = data.data;
                    console.dir(arr);
                    data_results.push({'id':'', 'text':''});
                    for (let index = 0; index < arr.length; index++) {
                        const element = arr[index];
                        data_results.push({'id':element['id'], 'text':element['name']});
                    }
                    return {
                        results: data_results,
                        pagination: {
                            more: (params.page * data.per_page) < data.total,
                        }
                    };
                },
            },
            width: '100%',
        });
        $('#account_select').select2({
            ajax: {
                url: '{{ route("kpis.accounts.search") }}',
                dataType: 'json',
                type: 'GET',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page
                    }
                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    console.dir(params.page);
                    let data_results = [];
                    let arr = data.data;
                    console.dir(arr);
                    data_results.push({'id':'', 'text':''});
                    for (let index = 0; index < arr.length; index++) {
                        const element = arr[index];
                        data_results.push({'id':element['id'], 'text':element['name']});
                    }
                    return {
                        results: data_results,
                        pagination: {
                            more: (params.page * data.per_page) < data.total,
                        }
                    };
                },
            },
            width: '100%',
        });
        $('#employee_select').select2({
            ajax: {
                url: '{{ route("kpis.employees.search") }}',
                dataType: 'json',
                type: 'GET',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page
                    }
                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    let data_results = [];
                    let arr = data.data;
                    console.dir(arr);
                    data_results.push({'id':'', 'text':''});
                    for (let index = 0; index < arr.length; index++) {
                        const element = arr[index];
                        data_results.push({'id':element['id'], 'text':element['first_name'] + ' ' + element['last_name'] + ' : ' + element['phone'] + ' : ' + element['profile'] + ' : ' + element['account']['name']});
                    }
                    return {
                        results: data_results,
                        pagination: {
                            more: (params.page * data.per_page) < data.total,
                        }
                    };
                },
            },
            width: '100%',
        });
        $('#processTypes').change(function() {
            $('.dinamic_select').attr('disabled','disabled');
            let id = $(this).val() ? $(this).val() : 0;
            type = $processTypes[id].split('_')[1];
            var selectCheck = document.body.querySelector('#'+type+'_select');
            if($.contains( document.body, selectCheck )) {
                $(selectCheck).attr('disabled',false);
            };
        });
    </script>

@stop
