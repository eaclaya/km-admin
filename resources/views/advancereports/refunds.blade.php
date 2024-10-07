@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.refunds')}}">
              @csrf
				 <div class="col-md-3">
					<p>Fecha Inicio</p>
					<input type="date" name="from_date" class="form-control" />
                                </div>
				<div class="col-md-3">
					<p>Fecha Fin</p>
					<input type="date" name="to_date" class="form-control" />
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select  name="export" class="form-control">
						<option value="0">Filtrar</option>
                                                <option value="1">Exportar</option>
                                        </select>
                                </div>
				<div class="col-md-3">
					<p>&nbsp;</p>
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
						<td>Fecha</td>
						<td>Factura</td>
						<td>Codigo</td>
						<td>Proveedor</td>
						<td>Descripcion</td>
						<td>Vendedor</td>
						<td>Cliente</td>
						<td>Telefono</td>
						<td>Tienda</td>
						<td>Cantidad</td>
						<td>Costo</td>
						<td>Precio</td>
						<td>Total</td>
						<td>Total Costo</td>
						<td>Total facturado</td>
						<td>Tipo devolucion</td>
						<td>Accion</td>
						<td>Razon de devolucion</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['date']}}</td>
						<td>{{$item['invoice_number']}}</td>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['vendor']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['client']}}</td>
						<td>{{$item['phone']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['qty']}}</td>
						<td>{{$item['product_cost']}}</td>
						<td>{{$item['cost']}}</td>
						<td>{{$item['total']}}</td>
						<td>{{$item['total_cost']}}</td>
						<td>{{$item['total_invoice']}}</td>
						<td>{{$item['tipo']}}</td>
						<td>{{$item['cause']}}</td>
						<td>{{$item['return_reason']}}</td>
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
    </script>
@stop


