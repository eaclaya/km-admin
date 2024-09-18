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
    <div class="tracesrequests">
        <hr> 
        <div class="row">
            <div class="col-md-12">
                <form class="filter-form" method="POST" action="{{route('advancereports.traces_request')}}">
              @csrf
                    <div class="col-md-2">
                            <p>Fecha inicio</p>
                            <input type="date" class="form-control" name="from_date" />
                    </div>
                    <div class="col-md-2">
                            <p>Fecha fin</p>
                            <input type="date" class="form-control" name="to_date" />
                    </div>
                    <div class="col-md-2">
                        <p>Areas</p>
                        <select id="area" name="area" class="control-form form-control">
                                <option value="all">Todas</option>
                                @foreach($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <p>Tiendas</p>
                        <select id="store" name="store" class="control-form form-control">
                                <option value="all">Todas</option>
                                @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <p>&nbsp;</p>
                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>
    </div>
    

    <script src="{{asset('js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready( function () {
            $('table#table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
        $("#area").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>

@stop
