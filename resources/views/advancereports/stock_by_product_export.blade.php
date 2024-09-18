<table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td>Tienda</td>
                                                <td>Cantidad</td>
                                                <td>Precio Normal</td>
                                                <td>Precio Mayorista</td>
                                                <td>Precio Especial</td>
                                                <td>Ultima Factura</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['qty']}}</td>
                                                <td>{{$item['price']}}</td>
                                                <td>{{$item['wholesale_price']}}</td>
                                                <td>{{$item['special_price']}}</td>
                                                <td>{{$item['last_invoice']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
