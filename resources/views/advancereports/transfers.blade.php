@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.transfers')}}">
              @csrf
				<div class="col-md-2">
					<p>Fecha inicio</p>
					<input type="date" class="form-control" name="from_date" />
				</div>
				<div class="col-md-2">
					<p>Fecha fin</p>
					<input type="date" class="form-control" name="to_date" />
				</div>
				<div class="col-md-2">
					<p>Tienda Origen</p>
					<select id="from_store" name="from_store" class="control-form form-control">
						<option value="all">Todas</option>
						@foreach($stores as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<p>Tienda Destino</p>
					<select id="to_store" name="to_store" class="control-form form-control">
						<option value="all">Todas</option>
						@foreach($stores as $store)
							<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<p>Tipo</p>
					<select id="completed" name="completed" class="control-form form-control">
						<option value="1">Completadas</option>
						<option value="2">Pendientes</option>
					</select>
				</div>
				<div class="col-md-2">
						<p>&nbsp;</p>
						<button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
				</div>
			</form>
		</div>
	</div>
	<hr>
	<script>
		$("#from_store").chosen({
			disable_search_threshold: 10,
			no_results_text: "Oops, nothing found!",
			width: "100%"
		});
		$("#to_store").chosen({
			disable_search_threshold: 10,
			no_results_text: "Oops, nothing found!",
			width: "100%"
		});
	</script>
@stop


