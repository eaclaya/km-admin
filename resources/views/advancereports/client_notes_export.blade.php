<table class="table">
                                <thead>
                                        <tr>
                                                <td>Cliente</td>
                                                <td>Usuario</td>
                                                <td>Descripcion</td>
                                                <td>Fecha</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['user']}}</td>
                                                <td>{{$item['description']}}</td>
                                                <td>{{$item['created_at']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
