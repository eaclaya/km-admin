 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Telefono</td>
                                                <td>Empleado</td>
                                                <td>Tienda</td>
                                                <td>Perfil</td>
                                                <td>Descripcion</td>
                                                <td>Pineado</td>
                                                <td>Facturas</td>
                                                <td>Fecha</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['phone']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['profile']}}</td>
                                                <td>{{$item['description']}}</td>
                                                <td>{{$item['pinned']}}</td>
                                                <td>{{$item['invoices']}}</td>
                                                <td>{{$item['created_at']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>