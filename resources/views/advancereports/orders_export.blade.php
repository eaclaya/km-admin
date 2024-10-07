 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Numero de encargo</td>
                                                <td>Tienda</td>
                                                <td>Usuario</td>
                                                <td>Cliente</td>
                                                <td>Fecha de encargo</td>
                                                <td>Total de encargo</td>
                                                <td>Vendedor</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['order_number']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['user']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['date']}}</td>
                                                <td>{{$item['amount']}}</td>
                                                <td>{{$item['seller']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
