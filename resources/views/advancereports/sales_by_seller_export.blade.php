<table class="table">
                                <thead>
                                        <tr>
                                                <td>Vendedor</td>
                                                <td>Perfil</td>
                                                <td>Tienda</td>
                                                <td>Cliente</td>
                                                <td>Telefono de cliente</td>
                                                <td>Fecha de factura</td>
                                                <td>Fecha de pago</td>
                                                <td>Tipo de factura</td>
                                                <td>Factura</td>
                                                <td>Total</td>
                                                <td>Comision</td>
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
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['invoice_date']}}</td>
                                                <td>{{$item['payment_date']}}</td>
                                                <td>{{$item['invoice_status']}}</td>
                                                <td>{{$item['invoice_number']}}</td>
                                                <td>{{$item['amount']}}</td>
                                                <td>{{$item['commission']}}</td>
                                                <td>{{$item['total_cost']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
