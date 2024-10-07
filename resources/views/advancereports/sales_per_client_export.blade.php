  <table class="table">
                                <thead>
                                        <tr>
                                                <td>Tienda</td>
                                                <td>Total Transacciones</td>
                                                <td>Total Clientes</td>
                                                <td>Total Facturado</td>
                                                <td>Promedio x Cliente</td>
                                                <td>Promedio x Transaccion</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->account}}</td>
                                                <td>{{$item->invoice_count}}</td>
                                                <td>{{$item->client_count}}</td>
                                                <td>{{number_format($item->invoice_amount, 2, '', '')}}</td>
                                                <td>{{number_format($item->invoice_amount/$item->client_count, 2, '', '')}}</td>
                                                <td>{{number_format($item->invoice_amount/$item->invoice_count, 2, '', '')}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
