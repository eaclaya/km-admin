 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Auxiliar SAC</td>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Tienda</td>
                                                <td>Direccion</td>
                                                <td>Vendedor</td>
						<td>Factura</td>
                                                <td>Fecha Factura</td>
						<td>Costo</td>
                                                <td>Total</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['employee_sac']}}</td>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['account_name']}}</td>
                                                <td>{{$item['address']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['invoice']}}</td>
                                                <td>{{ $item['invoice_date'] }}</td>
                                                <td>{{$item['total_cost']}}</td>
                                                <td>{{$item['total']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
