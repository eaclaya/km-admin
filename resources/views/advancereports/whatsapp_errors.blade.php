
@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('whatsapp_error_report')}}">
              @csrf
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select id="store" name="store" class="control-form form-control" required>
						<option value="all">Todas</option>
						@foreach ($accounts as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
					<input type="date" name="date" id="date" value="{{$date}}">
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
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

