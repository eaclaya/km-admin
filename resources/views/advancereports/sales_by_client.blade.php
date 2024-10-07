@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.sales_by_client')}}">
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
                                        <p>Tipo de cliente</p>
                                        <select  name="type" class="form-control">
                                                <option value="Mayorista">Mayorista</option>
                                                <option value="Normal">Normal</option>
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
						<td>Cliente</td>
						<td>Telefono</td>
						<td>Tienda</td>
						<td>Direccion</td>
						<td>Vendedor</td>
						<td>Perfil</td>
						<td>Total contado</td>
						<td>Total credito</td>
						<td>Facturas</td>
						<td>Costo</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['name']}}</td>
						<td>{{$item['phone']}}</td>
						<td>{{$item['account_name']}}</td>
						<td>{{$item['address']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['profile']}}</td>
						<td>{{$item['total']}}</td>
						<td>{{$item['credit']}}</td>
						<td>{{$item['invoices']}}</td>
						<td>{{$item['total_cost']}}</td>
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


