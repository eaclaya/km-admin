
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>ID</td>
						<td>Hecho por</td>
						<td>Creada</td>
						<td>Asignada A Cierre</td>
						<td>Tienda</td>
						<td>Categoria</td>
						<td>Numero</td>
						<td>Tipo Pago</td>
						<td>Usuario</td>
						<td>ID Cierre</td>
						<td>Descripcion</td>
						<td>Total</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($result as $item)
						<tr>
							<td>{{ $item->id }}</td>
							<td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
							<td>{{ $item->created_at }}</td>
							<td>{{ $item->asig_date }}</td>
							<td>{{ $item->account_name }}</td>
							<td>{{ $item->ca_name }}</td>
							<td>{{ $item->number }}</td>
							<td>{{ substr($item->py_name, 0, 20) }}</td>
							<td>{{ $item->user_name .' '.$item->user_name2 }}</td>
							<td>{{ $item->cash_count_id }}</td>
							<td>{{ $item->description }}</td>
							<td>{{ $item->amount }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

