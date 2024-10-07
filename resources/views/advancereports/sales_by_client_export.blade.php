 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Tienda</td>
                                                <td>Direccion</td>
                                                <td>Vendedor</td>
                                                <td>Perfil</td>
                                                <td>Total contado</td>
						<td>Total credito</td>
						<td>Facturas</td>
						<td>Costo</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['account_name']}}</td>
                                                <td>{{$item['address']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['profile']}}</td>
                                                <td>{{$item['total']}}</td>
                                                <td>{{$item['credit']}}</td>
                                                <td>{{$item['invoices']}}</td>
                                                <td>{{$item['total_cost']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
