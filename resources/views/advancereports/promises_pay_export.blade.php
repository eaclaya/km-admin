@if (isset($results))
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped dataTable" style="font-size:12px">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ruta</th>
                    <th>Vendedor</th>
                    <th>Cliente</th>
                    <th>Telefono</th>
                    <th>Estado</th>
                    <th>Bloqueo fecha</th>
                    <th>Bloqueado por</th>
                    <th>Deuda</th>
                    <th>Limite credito</th>
                    <th>Desbloqueo fecha</th>
                    <th>Desbloqueo por</th>
                    <th>Promesa de pago</th>
                    <th>Comentario Promesa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr >
                        <td>{{ $result->id }}</td>
                        <td>{{ $result->route_name??'' }}</td>
                        <td>{{ $result->seller_name1 }} {{ $result->seller_name2 }}</td>
                        <td>{{ $result->client_name.' - '. $result->company_name }}--{{ $result->blocked_credit? ' -BLOQUEADO':'' }}</td>
                        <td>{{ $result->phone }}</td>
                        <td>{{ $result->is_blocked? 'Bloqueado': 'Desbloqueado' }}</td>
                        <td>{{ $result->blocked_at }}</td>
                        <td>{{ $result->blocked_by }}</td>
                        <td>{{ $result->balance }}</td>
                        <td>{{ $result->limit_credit }}</td>
                        <td>{{ $result->unlocked_at }}</td>
                        <td>{{ $result->unlocked_by == '0'? 'Automatico': $result->username }}</td>
                        <td>{{ $result->payment_promise }}</td>
                        <td>{{ $result->comments_promise }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
   