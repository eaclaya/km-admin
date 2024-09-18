@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.devices')}}">
              @csrf
				 <div class="col-md-3">
					<p>&nbsp;</p>
                                        <select class="form-control" name="export">
                                                <option value="0">Ver resultados</option>
                                                <option value="1">Exportar</option>
                                        </select>
                                </div>
				<div class="col-md-3">
					<p>&nbsp;</p>
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
						<td>Usuario</td>
						<td>IP</td>
						<td>token</td>
						<td>Dispositivo</td>
						<td>Navegador</td>
						<td>Plataform</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item->email}}</td>
						<td>{{$item->remote_ip}}</td>
						<td>{{$item->token}}</td>
						<td>{{$item->device}}</td>
						<td>{{$item->browser}}</td>
                                                <td>{{$item->platform}}</td>
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


