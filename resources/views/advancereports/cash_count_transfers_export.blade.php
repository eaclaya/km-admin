
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>ID</td>
						<td>Hecho por</td>
						<td>Transaccion</td>
						<td>Fecha</td>
						<td>Tienda</td>
						<td>Cuenta Origen</td>
						<td>Cuenta Destino</td>
						<td>Usuario</td>
						<td>Estado</td>
						<td>Descripción</td>
						<td>ID from Cierre</td>
						<td>ID to Cierre</td>
						<td>Monto</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($result as $item)
						<tr>
							<td>{{ $item->id }}</td>
							<td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
							<td>{{ $item->document_number }}</td>
							<td>{{ $item->created_at }}</td>
							<td>{{ $item->account_name }}</td>
							<td>{{ $item->fa_name }}</td>
							<td>{{ $item->fat_name }}</td>

							<td>{{ $item->user_name .' '.$item->user_name2 }}</td>
							<td>@if ($item->completed)
								Completada
							@else
								Pendiente
							@endif</td>
							<td>{{ $item->description }}</td> 
							<td>{{ $item->cash_count_id }}</td>
							<td>{{ $item->cash_count_out_id }}</td>
							<td>{{ $item->amount }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

