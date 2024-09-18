@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('packings.transfers_in_packing')}}">
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
						<td>Descripcion</td>
						<td>Disponibilidad</td>
						<td>Cantidad Original</td>
						<td>Cantidad Final</td>
						<td>Diferencia</td>
						<td>Cantidad Equivalencias</td>
						<td>Tienda</td>
						<td>Equivalencias</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item->product_key}}</td>
						<td>{{$item->notes}}</td>
						<td>{{$item->product_qty}}</td>
						<td>{{$item->original_qty}}</td>
						<td>{{$item->packing_qty}}</td>
						<td>{{$item->packing_qty - $item->original_qty}}</td>
						<td>{{$item->relation_qty}}</td>
						@if($item->group == false)
						<td>{{$item->account_name}}</td>
						@else
						<td>N/A</td>
						@endif
						<td>{{$item->relation_id}}</td>
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


