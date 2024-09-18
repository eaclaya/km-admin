
@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	
	@if(isset($reportProcess))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Id de Proceso</td>
						<td>Id de Importacion</td>
						<td>Tipo de Proceso</td>
						<td>Estatus</td>
						<td>Porcentaje</td>
						<td>Fecha Creacion</td>
						<td>Fecha Finalización</td>
					</tr>
				</thead>
				<tbody>
					@foreach($reportProcess as $item)
					<tr>
						<td>
							{{$item->id}}
						</td>
						<td>
							<a href="{{ route('imports.edit', $item->file) }}">{{$item->file}}</a>
						</td>
						<td>
							@if ($item->report == 'import_tracking')
								Subida de Importacion
							@else
								Procesamiento de Importacion
							@endif
						</td>
						<td>
							@if (is_null($item->status) || $item->status == 0)
								En Proceso
								<br>
								<a href="{{route('advancereports.finish_report',['id' => $item->id])}}" class="btn btn-success btn-sm">Marcar Finalizado</a>
							@elseif($item->status == 1)
								Finalizado
							@else
								Error
							@endif
						</td>
						@php
							$item->count_rows = (is_null($item->count_rows) || $item->count_rows == 0) ? 0 : $item->count_rows;
							$item->rows = (is_null($item->rows) || $item->rows == 0) ? 1 : $item->rows;
							$porcentCompleting = ($item->count_rows * 100) / $item->rows;
							$porcentCompleting = round($porcentCompleting, 0);
							$porcentCompleting = ($porcentCompleting == 0 || $porcentCompleting == 1) ? 'Por Procesar el ' : ceil($porcentCompleting);
						@endphp
						<td>
							<strong>{{$porcentCompleting}}%</strong>
							@if (intval($porcentCompleting) < 100)
								<a href="{{url()->full()}}" class="btn btn-sm btn-primary">Recargar</a>
							@endif
						</td>
						<td>{{$item->created_at}}</td>
						<td>{{$item->updated_at}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@else
	<h1>
		No existen procesos de Importaciones
	</h1>
	@endif
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 1, "desc" ]]
            });
        } );
		$("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop

