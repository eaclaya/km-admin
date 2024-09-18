 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Factura</td>
                                                <td>Tienda</td>
                                                <td>Cliente</td>
                                                <td>Puntos Canjeados</td>
                                                <td>Fecha Factura</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->invoice_number}}</td>
                                                <td>{{$item->account}}</td>
                                                <td>{{$item->client}}</td>
                                                <td>{{$item->discount_points}}</td>
                                                <td>{{$item->invoice_date}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
