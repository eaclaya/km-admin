<table class="table">
                                <thead>
                                        <tr>
                                                <td>Numero de surtido</td>
                                                <td>Tienda</td>
                                                <td>Usuario</td>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Estado</td>
                                                <td>Comentario</td>
                                                <td>Cotizacion</td>
                                                <td>Factura</td>
                                                <td>Transferencia</td>
                                                <td>Packing</td>
                                                <td>Pedido</td>
                                                <td>Fecha de surtido</td>
                                                <td>Fecha de finalizacion</td>
                                                <td>Total de surtido</td>
                                                <td>Vendedor</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['supply_number']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['user']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['status']}}</td>
                                                <td>{{$item['comment']}}</td>
                                                <td>{{$item['quote']}}</td>
                                                <td>{{$item['invoice']}}</td>
                                                <td>{{$item['transfer']}}</td>
                                                <td>{{$item['packing']}}</td>
                                                <td>{{$item['request']}}</td>
                                                <td>{{$item['date']}}</td>
                                                <td>{{$item['end_date']}}</td>
                                                <td>{{$item['amount']}}</td>
                                                <td>{{$item['seller']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
