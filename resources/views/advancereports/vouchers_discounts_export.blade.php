
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>ID</td>
						<td>Tienda</td>
                        <td>Factura ID</td>
						<td>Factura</td>
                        <td>Cliente ID</td>
                        <td>Cliente</td>
                        <td>Fecha</td>
                        <td>Aprobo</td>
						<td>Ventas KMS</td>
						<td>%</td>
                        <td>Descuento</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						  <td>{{$item->id}}</td> 
						  <td>{{$item->accounts_name}}</td> 
						  <td>{{$item->invoice_id}}</td> 
						  <td>{{$item->invoice_number}}</td> 
						  <td>{{$item->client_id}}</td> 
						  <td>{{$item->clients_name}}</td> 
						  <td>{{$item->date_vouchers}}</td> 
						  <td>{{$item->accepted_by}}</td> 
						  <td>{{$item->invoice_kms_amount}}</td> 
						  <td>{{$item->percentage_amount}}</td> 
						  <td>{{$item->vouchers_amount}}</td> 
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

