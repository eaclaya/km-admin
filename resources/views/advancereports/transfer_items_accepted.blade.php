@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.transfer_items_accepted')}}">
              @csrf
				<div class="col-md-2">
					<label for="from_date">Fecha de Inicio</label>
					<input type="date" class="form-control" name="from_date" id="from_date" required/>
				</div>
				<div class="col-md-2">
					<label for="to_date">Fecha de Fin</label>
					<input type="date" class="form-control" name="to_date" id="to_date" required/>
				</div>
				<div class="col-md-2">
					<label for="from_account">Desde Tienda</label>
					<select id="from_account" name="from_account" class="control-form form-control" required>
						<option value="0">Todas</option>
						@foreach($stores as $store)
						<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<label for="to_account">Para Tienda</label>
					<select id="to_account" name="to_account" class="control-form form-control" required>
						<option value="0">Todas</option>
						@foreach($stores as $store)
						<option value="{{$store->id}}">{{$store->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
					<select  name="export" class="control-form form-control">
						<option value="0">Ver Resultados</option>
						<option value="1">Exportar</option>
					</select>
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
					<button type="submit" class="btn btn-primary btn-sm">CONTINUAR</button>
				</div>
			</form>
		</div>
	</div>
	<hr>
	@if(isset($result))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Codigo</td>
						<td>Descripcion</td>
						<td>Fecha</td>
						<td>Cantidad</td>
						<td>Numero de transferencia</td>
						<td>Origen</td>
						<td>Destino</td>
						<td>Proveedor</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['created_at']}}</td>
						<td>{{$item['qty']}}</td>
						<td>{{$item['transfer']}}</td>
						<td>{{$item['from_account']}}</td>
						<td>{{$item['to_account']}}</td>
						<td>{{$item['vendor']}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@endif
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        } );
		$("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop


