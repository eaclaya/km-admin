  <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td>Cantidad</td>
                                                <td>Precio de venta</td>
                                                <td>Precio de taller</td>
                                                <td>Factura</td>
                                                <td>Fecha</td>
                                                <td>Tienda</td>
                                                <td>Cliente</td>
                                                <td>Vendedor</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['quantity']}}</td>
                                                <td>{{$item['cost']}}</td>
                                                <td>{{$item['normal_price']}}</td>
                                                <td>{{$item['invoice_number']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['employee']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
