 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Mecanico</td>
                                                <td>Tienda</td>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td>Total</td>
                                                <td>Factura</td>
                                                <td>Fecha de factura</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['mechanic']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['total']}}</td>
                                                <td>{{$item['invoice_number']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
