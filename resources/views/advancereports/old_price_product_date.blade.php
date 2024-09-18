@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.old_price_product_date')}}">
              @csrf
				<div class="col-md-3">
					<p>Fecha inicio</p>
					<input type="month" class="form-control" name="from_date" />
				</div>
				<div class="col-md-3">
					<p>Tienda</p>
					<select class="form-control" name="store" id="store">
						@foreach($stores as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-6">
					<p>&nbsp;</p>
					<button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
				</div>
			</form>
		</div>
	</div>
	<hr>
	<script>
		$("#store").chosen({
			disable_search_threshold: 10,
			no_results_text: "Oops, nothing found!",
			width: "100%"
		});
	</script>
@stop


