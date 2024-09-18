 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Tienda</td>
                                                <td>Vendedor</td>
                                                <td>Fecha de creacion</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['created_at']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
