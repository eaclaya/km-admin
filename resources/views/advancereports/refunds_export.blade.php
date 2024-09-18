  <table class="table">
                                <thead>
                                        <tr>
                                                <td>Fecha</td>
						<td>Factura</td>
						<td>Codigo</td>
						<td>Proveedor</td>
						<td>Descripcion</td>
						<td>Vendedor</td>
						<td>Cliente</td>
						<td>Telefono</td>
						<td>Tienda</td>
						<td>Cantidad</td>
						<td>Costo</td>
						<td>Precio</td>
						<td>Total</td>
						<td>Total Costo</td>
						<td>Total facturado</td>
						<td>Tipo devolucion</td>
						<td>Accion</td>
						<td>Razon de devolucion</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['date']}}</td>
						<td>{{$item['invoice_number']}}</td>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['vendor']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['client']}}</td>
						<td>{{$item['phone']}}</td>
						<td>{{$item['account']}}</td>
						<td>{{$item['qty']}}</td>
						<td>{{$item['product_cost']}}</td>
						<td>{{$item['cost']}}</td>
						<td>{{$item['total']}}</td>
						<td>{{$item['total_cost']}}</td>
						<td>{{$item['total_invoice']}}</td>
						<td>{{$item['tipo']}}</td>
						<td>{{$item['cause']}}</td>
						<td>{{$item['return_reason']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
