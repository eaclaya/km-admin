
@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.unselled_products')}}">
              @csrf
				<div class="col-md-3">
                                        <p>Periodo</p>
					<select class="form-control" name="month_ago">
						<option value="1">1 Mes atras</option>
						<option value="3">3 Meses atras</option>
						<option value="6">6 Meses atras</option>
					</select>
                                </div>
                                <div class="col-md-3">
					<p>Tienda</p>
					<select class="form-control" name="account_id" id="account_id">
					<option>Todas</option>
					@foreach($accounts as $account)
						<option value="{{$account->id}}">{{$account->name}}</option>
					@endforeach
                                        </select>
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
						<td>Codigo</td>
						<td>Producto</td>
						<td>Costo</td>
						<td>Precio normal</td>
						<td>Precio mayorista</td>
						<td>Precio especial</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['cost']}}</td>
						<td>{{$item['price']}}</td>
						<td>{{$item['wholesale_price']}}</td>
						<td>{{$item['special_price']}}</td>
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
		$("#account_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop

