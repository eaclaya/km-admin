@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.imports_pending')}}">
              @csrf
				 <div class="col-md-3">
					<br/>
                                </div>
				<div class="col-md-3">
					<br/>
				</div>
				<div class="col-md-2">
					</br>
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
						<td>Identificador</td>
						<td>Fecha</td>
						<td>Tienda</td>
						<td>Estado</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td><a href="{{route('imports.edit', $item->id)}}">{{$item->id}}</a> </td>
						<td>{{$item->created_at}}</td>
						<td>{{$item->account}}</td>
						<td>Pendiente</td>
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


