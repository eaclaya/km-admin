@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.customers_points')}}">
              @csrf
				 <div class="col-md-6">
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
	@if(isset($result))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Cliente</td>
						<td>Telefono</td>
						<td>Puntos</td>
						<td>Direccion</td>
						<td>Empleado</td>
						<td>Ultima Factura</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['name']}}</td>
						<td>{{$item['phone']}}</td>
                                                <td>{{$item['points']}}</td>
                                                <td>{{$item['address']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
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


