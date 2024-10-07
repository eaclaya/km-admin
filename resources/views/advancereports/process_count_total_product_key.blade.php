@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<a href="{{url('count-total-product-key')}}" class="btn btn-success btn-sm">
				Procesar
			</a>
		</div>
	</div>
	<hr>
	@if(isset($reportProcess))
		@include('advancereports.parts.report_process_table',['reportProcess' => $reportProcess, 'showLink' => false])
	@endif
@stop


