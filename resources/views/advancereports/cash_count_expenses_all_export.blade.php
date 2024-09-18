
<div class="row">
	<div class="col-md-12">
		<table class="table">
			<thead>
				<tr>
					<td>Tienda</td>
					<td>Categoria</td>
					<td>Subategoria</td>
					<td>Monto</td>
				</tr>
			</thead>
			<tbody>
				@foreach ($result as $store => $resul)
					<tr>
						<td><strong>{{ $store }}</strong></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					@foreach ($resul as $categoria => $item)
						<tr>
							<td></td>
							<td>{{ $categoria }}</td>
							<td></td>
							<td></td>
						</tr>
						@foreach ($item as $subcategoria => $monto)
							<tr>
								<td></td>
								<td></td>
								<td>{{  $subcategoria }}</td>
								<td>{{ $monto }}.00</td>
							</tr>
						@endforeach
					@endforeach
				@endforeach
			</tbody>
		</table>
	</div>
</div>
