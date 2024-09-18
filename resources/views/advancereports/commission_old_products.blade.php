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
                <form class="filter-form" method="POST" action="{{route('advancereports.commission_old_products')}}">
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
                        <p>Tiendas</p>
                        <select id="store" name="store" class="control-form form-control">
                                <option value="">Todas</option>
                                @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <p>&nbsp;</p>
                        <button type="submit" class="btn btn-primary btn-block" onclick="disableButton(this)" >CONTINUAR</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        <p>Este reporte trae el total de las comisiones de 4% de productos con mas de 6 meses sin vender</p>
        <p>Si no selecciona la fecha se consultará la quincena actual por defecto</p>

        <hr>
        @if(isset($reportProcess))
            @include('advancereports.parts.report_process_table',['reportProcess' => $reportProcess, 'showLink' => true])
        @endif
    </div>
    

    <script src="{{asset('js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready( function () {
            $('table#table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });

        function disableButton(button) {
            button.disabled = true;
            button.innerText = 'Procesando...';
            button.form.submit(); // Envía el formulario
        }
    </script>

@stop
