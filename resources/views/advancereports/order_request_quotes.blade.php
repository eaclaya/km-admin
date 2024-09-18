@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('verifyorder.products_in_verify')}}">
              @csrf
				<div class="col-md-3">
					<p>Fecha inicio</p>
					<input type="date" class="form-control" name="from_date" />
				</div>
				<div class="col-md-3">
					<p>Fecha fin</p>
					<input type="date" class="form-control" name="to_date" />
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
					<select  name="group" class="form-control">
						<option value="0">No agrupar</option>
						<option value="1">Agrupar x producto</option>
					</select>
				</div>
				<div class="col-md-4">
					<p>&nbsp;</p>
					<select  name="export" class="select-group control-form form-control">
							<option value="0">Ver Resultados</option>
							<option value="1">Exportar</option>
					</select>
					<button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
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
						<th>Nro de Orden</th>
						<th>Tienda</th>
						<th>Codigo</th>
						<th>Descripcion</th>
						<th>Comentario</th>
						<th>Precio Mayorista</th>
						<th>Cantidad Solicitada</th>
						<th>Cantidad Solicitada Neta</th>
						<th>Cantidad Tienda</th>
						<th>Cantidad Equivalencias Tienda</th>
						<th>Cantidad Bodega</th>
						<th>Cantidad Equivalencias Bodega</th>
						<th>Ubicacion Bodega</th>
						<th>Equivalencias</th>
                    	<th>Cantidad Vendida del Producto</th>
				        <th>Cantidad Vendida de Equivalencia</th>
						<td>Proveedor</a>
						<th>Fecha</th>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item->order_id}}</td>
						<td>{{$item->account_name}}</td>
						<td>{{$item->product_key}}</td>
						<td>{{$item->description}}</td>
						<td>{{$item->comments}}</td>
						<td>{{$item->wholesale_price}}</td>
						<td>{{$item->qty}}</td>
						<td>{{$item->qty_total}}</td>
						<td>{{$item->actualQty}}</td>
	                    <td>{{$item->relation_qty}}</td>
						<td>{{$item->availableQty}}</td>
						<td>{{$item->relation_qty_warehouse}}</td>
						<td>{{$item->locationInWarehouse}}</td>
						<td>{{$item->relation_id}}</td>
						<td>{{$item->qty_product_sales}}</td>
						<td>{{$item->qty_relation_sales}}</td>
						<td>{{$item->vendor}}</td>
						<td>{{$item->created_at}}</td>
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
	});
    </script>
@stop


