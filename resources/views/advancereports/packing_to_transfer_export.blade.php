<table class="table">
	<thead>
		<tr>
			<td>Packing</td>
			<td>Transferencia</td>
			<td>Fecha</td>
			<td>Cantidad de productos</td>
			<td>Usuario</td>
		</tr>
	</thead>
	<tbody>
		@foreach($result as $item)
		<tr>
			<td>{{$item['packing']}}</td>
			<td>{{$item['transfer']}}</td>
			<td>{{$item['date']}}</td>
			<td>{{$item['items_qty']}}</td>
			<td>{{$item['user']}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
