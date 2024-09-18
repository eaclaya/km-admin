@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('goal.store_report')}}">
              @csrf
				<div class="col-md-2">
					<p>Desde</p>
					<input type="date" name="from_date"  class="form-control">
				</div>			
				<div class="col-md-2">
						<p>Hasta</p>
						<input type="date" name="to_date"  class="form-control">
				</div>	
				<div class="col-md-3">
					<p>Tipo de reporte</p>
					<select class="form-control" name="type">
						<option value="1">Quincenal</option>
						<option value="2">Mensual</option>
					</select>
				</div>
				<div class="col-md-5">
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
	
@stop


