<table class="table">
                                <thead>
                                        <tr>
                                                <td>Identificador</td>
                                                <td>Fecha</td>
                                                <td>Tienda</td>
                                                <td>Estado</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td>{{$item->account}}</td>
                                                <td>Pendiente</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
