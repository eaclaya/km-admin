	@if(isset($reportProcess))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>id</td>
						<td>Archivo</td>
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
							@if(isset($showLink) && $showLink !== false)
								@if ($item->status == 0)
									{{$item->file}}
								@else
									<a href="{{asset($item->file)}}">{{$item->file}}</a>
								@endif
							@else
								{{$item->file}}
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
								<a href="{{route('advancereports.export_error_report',['id' => $item->id])}}" class="btn btn-danger btn-sm">Descargar Errores</a>
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
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table.table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
	@endif


