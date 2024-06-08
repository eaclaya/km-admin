@extends('adminlte::page')

@section('content')
    @parent
    @if (Session::has('message'))
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
    <div class="checkLists">
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form class="filter-form" method="POST" action="{{route('checklist.reports')}}">
                    <div class="col-md-2">
                            <p>Fecha inicio</p>
                            <input type="date" class="form-control" name="from_date" />
                    </div>
                    <div class="col-md-2">
                            <p>Fecha fin</p>
                            <input type="date" class="form-control" name="to_date" />
                    </div>
                    <div class="col-md-2">
                        <p>&nbsp;</p>
                        <select id="store" name="store" class="control-form form-control">
                                <option value="0">Todas</option>
                                @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                            <p>&nbsp;</p>
                            <select  name="export" class="select-group control-form form-control">
                                    <option value="0">Ver Resultados</option>
                                    <option value="1">Exportar</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        @if(isset($checkDates))
            <table class="table">
                <thead>
                    <tr>
                        <td>Tienda</td>
                        @foreach ($dates as $date)
                        <td>{{$date}}</td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($checkDates as $account)
                    <tr>
                        @foreach($account as $key => $item)
                        <td>
                            @if ($key == 'name')
                                {{$item}}
                            @else
                                @if(!is_null($item))
                                    <a href="{{ route('checklist.edit', $item) }}">Si</a>
                                @else
                                    No
                                @endif
                            @endif

                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <h2>No hay registros</h2>
        @endif
    </div>


    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        } );
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>

@stop
