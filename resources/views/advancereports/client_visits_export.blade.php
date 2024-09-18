  <table class="table">
                                <thead>
                                        <tr>
                                                <td>Fecha</td>
                                                <td>Cliente</td>
                                                <td>Vendedor</td>
                                                <td>Longitud</td>
                                                <td>Latitude</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['created_at']}}</td>
                                                <td>{{$item['client']}}</td>
                                                <td>{{$item['employee']}}</td>
                                                <td>{{$item['longitude']}}</td>
                                                <td>{{$item['latitude']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
