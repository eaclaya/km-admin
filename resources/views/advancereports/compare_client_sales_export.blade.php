 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Tienda</td>
                                                <td>Direccion</td>
                                                <td>Vendedor</td>
                                                <td>Perfil</td>
                                                <td>Total Historico</td>
                                                <td>Total Actual</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['name']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['account_name']}}</td>
                                                <td>{{$item['address']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['profile']}}</td>
                                                <td>{{$item['total_history']}}</td>
                                                <td>{{$item['total_actual']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
