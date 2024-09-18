 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Description</td>
                                                <td>Numero de encargo</td>
                                                <td>Tienda</td>
                                                <td>Vendedor</td>
                                                <td>Usuario</td>
                                                <td>Cliente</td>
                                                <td>Fecha de encargo</td>
                                                <td>Cantidad</td>
                                                <td>Precio</td>
                                                <td>Cantidad en bodega</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['order_number']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['user']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['date']}}</td>
                                                <td>{{$item['qty']}}</td>
                                                <td>{{$item['cost']}}</td>
                                                <td>{{$item['qtyInWarehouse']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
