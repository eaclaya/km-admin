@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.stock_by_product')}}">
              @csrf
				<div class="col-md-3">
					<p>&nbsp;</p>
					<select id="store" name="store" class="control-form form-control">
						<option value="0">Todas</option>
                                                @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{$store->name}}</option>
                                                @endforeach
					</select>
				</div>
				<div class="col-md-3">
					<p>Filtrar por codigo</p>
                                        <input type="text" class="form-control" name="product_key" />
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
                                        <select  name="export" class="control-form control-form form-control">
                                                <option value="1">Exportar</option>
                                        </select>
				</div>
				<div class="col-md-3">
					<p>&nbsp;</p>
                                        <button type="submit" class="btn btn-primary btn-block" style="width: 100% !important">CONTINUAR</button>
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
						<td>Tienda</td>
						<td>Cantidad</td>
						<td>Precio Normal</td>
						<td>Precio Mayorista</td>
						<td>Precio Especial</td>
						<td>Ultima Factura</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['qty']}}</td>
						<td>{{$item['price']}}</td>
						<td>{{$item['wholesale_price']}}</td>
						<td>{{$item['special_price']}}</td>
						<td>{{$item['last_invoice']}}</td>
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


