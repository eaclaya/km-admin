@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop


@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.products_with_images')}}">
				@csrf
				<div class="col-md-2">
					<p>&nbsp;</p>
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
	
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
		$("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop


