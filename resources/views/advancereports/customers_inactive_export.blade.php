<table class="table">
                                <thead>
                                        <tr>
                                                <td>Empresa</td>
                                                <td>Contacto</td>
                                                <td>Telefono</td>
                                                <td>Tienda</td>
                                                <td>Vendedor</td>
                                                <td>Perfil</td>
                                                <td>Numero de Facturas Historico</td>
                                                <td>Ultima Factura</td>
                                                <td>Puntos</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['firstname']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['profile']}}</td>
                                                <td>{{$item['invoicesQtyTotal']}}</td>
                                                <td>{{$item['lastPurchaseDate']}}</td>
                                                <td>{{$item['points']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
