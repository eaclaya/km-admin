 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Puntos</td>
                                                <td>Direccion</td>
                                                <td>Empleado</td>
                                                <td>Tienda</td>
                                                <td>Ultima Factura</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['points']}}</td>
                                                <td>{{$item['address']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
