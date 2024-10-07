@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.account_settings')}}">
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
						<td>Nombre Completo</td>
						<td>RTN</td>
						<td>Sitio Web</td>
						<td>Correo Electronico</td>
						<td>Telefono</td>
						<td>Zona</td>
						<td>Logo</td>
						<td>Direccion</td>
						<td>Bloq/Pta</td>
						<td>Ciudad</td>
						<td>Región/Provincia</td>
						<td>Código Postal</td>
						<td>Nombre Casa Matriz</td>
						<td>Dirección Casa Matriz</td>
						<td>Firma del correo</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['name']}}</td>
						<td>{{$item['vat_number']}}</td>
						<td>
							@if($item['website'] !== 'Sin Asignar')
								<a href="{{'https://'.$item['website']}}" target="_blank" class="btn btn-sm btn-success">Ver Sitio Web</a>
							@else
								{{$item['website']}}
							@endif
						</td>
						<td>{{$item['work_email']}}</td>
						<td>{{$item['work_phone']}}</td>
						<td>{{$item['zone']}}</td>
						<td>
							@if($item['logo'] !== 'Sin Asignar')
								<a href="{{'https://'.$item['logo']}}" target="_blank" class="btn btn-sm btn-success">Ver Logo</a>
							@else
								{{$item['logo']}}
							@endif
						</td>
						<td>{{$item['address1']}}</td>
						<td>{{$item['address2']}}</td>
						<td>{{$item['city']}}</td>
						<td>{{$item['state']}}</td>
						<td>{{$item['postal_code']}}</td>
						<td>{{$item['Matrix_name']}}</td>
						<td>{{$item['Matrix_address']}}</td>
						<td>{{$item['email_footer']}}</td>
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


