
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
						<td>Cta Tienda</td>
						<td>Usuario</td>
						<td>Cuenta</td>
						<td>Subcuenta</td>
						<td>Aprobado</td>
						<td>Descripción</td>
						<td>Monto</td>
						<td>ID Cierre</td>
					</tr>
				</thead>
				<tbody>
					@foreach ($result as $item)
						<tr>
							<td>{{ $item->id }}</td>
							<td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
							<td>{{ $item->created_at }}</td>
							<td>{{ $item->expense_date }}</td>
							<td>{{ $item->account_name }}</td>
							<td>{{ $item->fa_name }}</td>
							<td>{{ $item->user_name .' '.$item->user_name2 }}</td>
							<td>{{ $item->categorie_name }}</td>
							<td>{{ $item->code .' '.$item->subcategorie_name }}</td>
							<td>{{ $item->is_approved?'si':'No'  }}</td>
							<td>{{ $item->public_notes }}</td> 
							<td>{{ $item->amount }}</td>
							<td>{{ $item->cash_count_id }}</td>

						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

