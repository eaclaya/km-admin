    @if (isset($result))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Ruta</td>
                            <td>Usuario</td>
                            <td>Clientes</td>
                            <td>Visitas</td>
                            <td>Visitas de otro dia</td>
                            <td>Porcentaje</td>
                            <td>Fecha</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['user'] }}</td>
                                <td>{{ $item['clients'] }}</td>
                                <td>{{ $item['visits'] }}</td>
                                <td>{{ $item['other_visits'] }}</td>
                                <td>{{ $item['percentage'] }}</td>
                                <td>{{ $item['fecha'] }}</td>
                            </tr>
                            <tr>
                                <th>-</th>
                                <th>-</th>
                                <th>Nombre</th>
                                <th>Compañia</th>
                                <th>Telefono</th>
                                <th>Resultado visita</th>
                                <td>Fecha y hora</td>
                            </tr>
                            @foreach ($item['clients_today']??[] as $key => $i)
                                <tr>
                                    <td></td>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $i['name'] }}</td>
                                    <td>{{ $i['company_name'] }}</td>
                                    <td>{{ $i['phone'] }}</td>
                                    <td>{{ $i['result_visit'] }}</td>
                                    <td>{{ $i['created_at'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
   