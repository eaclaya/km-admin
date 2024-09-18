@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.sales_by_seller')}}">
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
					<p>&nbsp;</p>
					<select id="store" name="store" class="select-group control-form form-control" required>
						<option value="all">Todas</option>
						@foreach ($accounts as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select  name="export" class="select-group control-form form-control">
						<option value="1">Exportar</option>
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
    <script>
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop


