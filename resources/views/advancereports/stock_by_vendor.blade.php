@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" id="form-submit" method="POST" action="{{route('advancereports.stock_by_vendor')}}">
              @csrf
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select id="vendor" name="vendor">
						<option value="">Seleccione un Proveedor</option>
						@foreach($vendors as $vendor)
							<option value="{{$vendor->id}}">{{$vendor->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select  name="group" class="control-form form-control">
						<option value="1">Agrupar por codigo</option>
						<option value="0">No agrupar por codigo</option>
					</select>
				</div>
				<div class="col-md-6">
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
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
			formSubmit = document.getElementById("form-submit");
			formSubmit.addEventListener("submit", function(event) {
				var vendor = document.getElementById("vendor").value;
				console.dir(vendor.trim());
				if (vendor.trim() == '') {
					alert('Elija un proveedor para continuar');
					event.preventDefault();
					return false;
				}
			});
        });
		$("#vendor").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
		
    </script>
@stop


