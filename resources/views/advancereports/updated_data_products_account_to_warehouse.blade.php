@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<a href="{{url('update-data-products-account')}}" class="btn btn-success btn-sm">
				Procesar
			</a>
		</div>
	</div>
	<hr>
	@if(isset($reportProcess))
		@include('advancereports.parts.report_process_table',['reportProcess' => $reportProcess, 'showLink' => false])
	@endif
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table.table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
@stop


