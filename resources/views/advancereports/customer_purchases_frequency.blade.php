@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.customer_purchases_frequency')}}">
              @csrf
				 <div class="col-md-3">
                                        <p>Fecha inicio</p>
                                        <input type="date" class="form-control" name="from_date" />
                                </div>
                                <div class="col-md-3">
                                        <p>Fecha fin</p>
                                        <input type="date" class="form-control" name="to_date" />
                                </div>
                                <div class="col-md-6">
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
						<td>Empresa</td>
						<td>Contacto</td>
						<td>Tipo</td>
						<td>Vendedor</td>
						<td>Telefono</td>
						<td>Cantidad Facturada</td>
						<td>Total Facturado</td>
						<td>Cantidad Facturada Historico</td>
						<td>Total Facturado Historico</td>
						<td>Ultima Factura</td>
						<td>Puntos</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['name']}}</td>
						<td>{{$item['firstname']." ".$item['lastname']}}</td>
						<td>{{$item['type']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['phone']}}</td>
						<td>{{$item['invoicesQty']}}</td>
						<td>{{$item['invoicesRevenue']}}</td>
						<td>{{$item['invoicesQtyTotal']}}</td>
						<td>{{$item['invoicesRevenueTotal']}}</td>
						<td>{{$item['lastPurchaseDate']}}</td>
                                                <td>{{$item['points']}}</td>
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


