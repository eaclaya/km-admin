@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.clients_by_date')}}">
              @csrf
				<div class="col-md-3">
                                        <p>Fecha Inicio</p>
                                        <input type="date" class="form-control" name="from_date" />
                                </div>
				<div class="col-md-3">
					<p>Fecha Fin</p>
					<input type="date" class="form-control" name="to_date" />
				</div>
				 <div class="col-md-3">
					<p>&nbsp;</p>
					<select class="form-control" name="export">
						<option value="0">Visualizar</option>
                                                <option value="1">Exportar</option>
                                        </select>
                                </div>
				<div class="col-md-3">
					<p>&nbsp;</p>
                                        <button type="submit" class="btn btn-primary btn-block">FILTRAR</button>
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
						<td>Vendedor</td>
						<td>Fecha de creacion</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['name']}}</td>
						<td>{{$item['phone']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['employee']}}</td>
                                                <td>{{$item['created_at']}}</td>
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


