
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Tienda</td>
                        <td>Cliente ID</td>
                        <td>Cliente</td>
                        <td>Telefono</td>
						<td>Ventas KMS</td>
						<td>%</td>
                        <td>Descuento</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						  <td>{{$item->account}}</td> 
						  <td>{{$item->id}}</td> 
						  <td>{{$item->name}}</td> 
						  <td>{{$item->phone}}</td> 
						  <td>{{$item->amount_vouchers_kms}} </td> 
						  <td>{{$item->percentage_vouchers}} </td> 
						  <td>{{$item->vouchers_discount}} </td> 
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

