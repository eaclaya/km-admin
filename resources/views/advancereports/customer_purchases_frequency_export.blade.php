 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Empresa</td>
                                                <td>Contacto</td>
                                                <td>Tipo</td>
                                                <td>Vendedor</td>
                                                <td>Telefono</td>
                                                <td>Cantidad Facturada</td>
                                                <td>Total Facturado</td>
                                                <td>Cantidad Facturada Historico</td>
                                                <td>Total Facturado Historico</td>
                                                <td>Ultima Factura</td>
                                                <td>Puntos</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
						<td>{{$item['firstname']." ".$item['lastname']}}</td>
						<td>{{$item['type']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['invoicesQty']}}</td>
                                                <td>{{$item['invoicesRevenue']}}</td>
                                                <td>{{$item['invoicesQtyTotal']}}</td>
                                                <td>{{$item['invoicesRevenueTotal']}}</td>
                                                <td>{{$item['lastPurchaseDate']}}</td>
                                                <td>{{$item['points']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
