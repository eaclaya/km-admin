<table class="table">
                                <thead>
                                        <tr>
                                                <td>Vendedor</td>
                                                <td>Perfil</td>
                                                <td>Tienda</td>
                                                <td>Cliente</td>
                                                <td>Fecha de Pago</td>
                                                <td>Tipo de Factura</td>
                                                <td>Fecha de Factura</td>
                                                <td>Factura</td>
                                                <td>Pago</td>
                                                <td>Monto de Factura</td>
                                                <td>Costo</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['profile']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['payment_date']}}</td>
                                                <td>{{$item['invoice_status']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
                                                <td>{{$item['invoice_number']}}</td>
                                                <td>{{$item['amount']}}</td>
                                                <td>{{$item['total_amount']}}</td>
                                                <td>{{$item['total_cost']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
