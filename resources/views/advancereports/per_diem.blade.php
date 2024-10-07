@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.per_diem') }}">
                <div class="col-md-3">
                    <p>Fecha inicio creado</p>
                    <input type="date" class="form-control" name="from_date"/>
                </div>
                <div class="col-md-3">
                    <p>Fecha fin creado</p>
                    <input type="date" class="form-control" name="to_date" />
                </div>
                <div class="col-md-3">
                    <p>Empleados</p>
                    <select id="employee_id" name="employee_id" class="select-group control-form form-control">
                        <option value="">Todos</option>
                        @foreach($employees as $employee)
                            <option value="{{$employee->id}}">{{$employee->first_name}} {{$employee->last_name}} - {{$employee->id_number}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <p>&nbsp;</p>
                    <select name="export" class="select-group control-form form-control">
                        <option value="0">Ver Resultados</option>
                        <option value="1">Exportar</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                </div>
            </form>
        </div>
    </div>
    <hr>
    @if (isset($results))
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped dataTable" style="font-size:12px">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Estado</th>
                            <th>Total Solicitado</th>
                            <th>Total Pagado</th>
                            <th>Total Declarado</th>
                            <th>Total Cuadre</th>
                            <th>Fecha Creado</th>
                            <th>Fecha Pagado</th>
                            <th>User Creador</th>
                            <th>User Aprovador</th>
                            <th>User Pagador</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $result)
                            <tr >
                                <td>{{ $result['id'] }}</td>
                                <td>{{ $result['name1'] }} {{ $result['name2'] }}</td>
                                <td>{{ $result['status'] }}</td>
                                <td>{{ $result['total_amount'] }}</td>
                                <td>{{ $result['total_amount_paid'] }}</td>
                                <td>{{ $result['total_declared'] }}</td>
                                <td>{{ $result['total_cuadre'] }}</td>
                                <td>{{ $result['created_at'] }}</td>
                                <td>{{ $result['date_paid'] }}</td>
                                <td>{{ $result['user_create'] }}</td>
                                <td>{{ $result['user_aproved'] }}</td>
                                <td>{{ $result['user_paid'] }}</td>
                                <td>{{ $result['generals_comments'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#employee_id').select2({width: '100%'});
            $('table').DataTable({
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>
@stop
