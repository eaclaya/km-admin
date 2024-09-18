
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>ID</td>
						<td>Hecho por</td>
						<td>Tienda</td>
						<td>Tipo</td>
						<td>Numero</td>
						<td>Fecha</td>
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
							<td>{{ $item->account_name }}</td>
							<td>@if(strpos($item->number, 'MT-') !== false)
								Transferencia
								@elseif(strpos($item->number, 'I-0') !== false)
								Otro Ingreso 
								@elseif(isset($item->store))
								Credito Adelanto 
								@else
								Pago Factura  
								@endif
							</td>
							<td>{{ $item->number }}</td>
							<td>{{ $item->created_at}}</td>
							<td>{{ isset($item->fa_name)? $item->fa_name : '' }}</td>
							<td>{{ $item->fat_name }}</td>

							<td>{{ $item->user_name .' '.$item->user_name2 }}</td>
							<td>
									@if ($item->completed)
										Completada
									@else
										Pendiente
									@endif
							</td>
							<td>{{ isset($item->description)? $item->description : '' }}</td> 
							<td>{{ $item->cash_count_id }}</td>
							<td>{{ isset($item->cash_count_out_id)?$item->cash_count_out_id:'' }}</td>
							<td>{{ $item->amount }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

