
@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.most_requested_products')}}">
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
						<td>Codigo</td>
						<td>Producto</td>
						<td>Unidades vendidas</td>
						<td>Disponible</td>
						<td>Tiendas</td>
						<td>Fecha</td>
						<td>Equivalencias</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['count']}}</td>
						<td>{{$item['available']}}</td>
						<td>{{$item['accounts']}}</td>
						<td>{{$item['created_at']}}</td>
						<td>{{$item['relation_id']}}</td>
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
