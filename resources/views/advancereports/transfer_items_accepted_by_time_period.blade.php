@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.transfer_items_accepted_by_time_period')}}">
              @csrf
				<div class="col-md-2">
					<label for="from_date">Fecha de Inicio</label>
					<input type="date" class="form-control" name="from_date" id="from_date" required/>
				</div>
				<div class="col-md-2">
					<label for="to_date">Fecha de Fin</label>
					<input type="date" class="form-control" name="to_date" id="to_date" required/>
				</div>
				<div class="col-md-2">
					<label for="store">Tienda</label>
					<select id="store" name="store" class="control-form form-control" required>
						@foreach($stores as $store)
						<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
					<select  name="export" class="control-form form-control">
						<option value="0">Ver Resultados</option>
						<option value="1">Exportar</option>
					</select>
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
					<button type="submit" class="btn btn-primary btn-sm">CONTINUAR</button>
				</div>
			</form>
		</div>
	</div>
	<hr>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
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


