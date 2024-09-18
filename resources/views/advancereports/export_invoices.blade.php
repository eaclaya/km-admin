@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop

@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.export_invoices')}}">
              @csrf
				<div class="col-md-3">
						<p>Fecha inicio</p>
						<input type="date" class="form-control" name="from_date" />
				</div>
				<div class="col-md-3">
						<p>Fecha fin</p>
						<input type="date" class="form-control" name="to_date" />
				</div>
				<div class="col-md-3">
					<p>Tiendas</p>
					<select id="store" name="store" class="control-form form-control" required>
						<option value="all">Todas</option>
						@foreach ($accounts as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<p>CAI</p>
					<select  id="filter" name="filter" class="select-group control-form form-control">
						<option value="all">todos</option>
						<option value="1">Solo con CAI</option>
						<option value="2">Solo sin CAI</option>
					</select>
					<button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
				</div>
			</form>
		</div>
	</div>
	<hr>
	@if(isset($reportProcess))
		@include('advancereports.parts.report_process_table',['reportProcess' => $reportProcess, 'showLink' => true])
	@endif
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 2, "desc" ]]
            });
        } );
		$("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop

