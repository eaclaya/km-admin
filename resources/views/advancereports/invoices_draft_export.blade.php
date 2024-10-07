	@if(isset($result))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Factura</td>
						<td>Fecha</td>
						<td>Total</td>
						<td>Tienda</td>
						<td>Vendedor</td>
						<td>Cliente</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['invoice_number']}}</td>
						<td>{{$item['invoice_date']}}</td>
						<td>{{$item['amount']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['client']}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@endif
