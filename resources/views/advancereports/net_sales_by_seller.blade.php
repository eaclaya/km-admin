@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.net_sales_by_seller')}}">
              @csrf
				 <div class="col-md-3">
					<p>Fecha inicio</p>
					<select class="form-control" name="from_date">
					@foreach($dates as $item)
					<option value="{{$item->from_date}}">{{$item->from_date}}</option>
					@endforeach
					</select>
                                </div>
                                <div class="col-md-3">
					<p>Fecha fin</p>
					<select class="form-control" name="to_date">
                                        @foreach($dates as $item)
                                        <option value="{{$item->to_date}}">{{$item->to_date}}</option>
                                        @endforeach
                                        </select>
				</div>
                                <div class="col-md-4">
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
	<p>Este reporte se alimenta solamente de las planillas por lo cual no cuadrará con los graficos</p>
	<hr>
	@if(isset($result))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Vendedor</td>
						<td>Perfil</td>
						<td>Tienda</td>
						<td>Ventas Base</td>
						<td>Credito De Tienda</td>
						<td>Devoluciones</td>
						<td>Ventas Netas</td>
						<td>Comision</td>
						<td>Fecha Inicio</td>
						<td>Fecha Fin</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['employee']}}</td>
						<td>{{$item['profile']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['base_sales']}}</td>
						<td>{{$item['store_credit']}}</td>
						<td>{{$item['refunds']}}</td>
						<td>{{$item['sales']}}</td>
						<td>{{$item['commission_amount']}}</td>
						<td>{{$item['from_date']}}</td>
						<td>{{$item['to_date']}}</td>
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
	    "order": [[ 0, "desc" ]],
		    "paging": false
            });
        } );
    </script>
@stop


