@if (isset($result))
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Tienda</td>
                    <td>Ruta</td>
                    <td>Cliente</td>
                    <td>Empresa</td>
                    <td>Tipo</td>
                    <td>Telefono</td>
                    <td>Direccion</td>
                    <td>Dia Visita</td>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($result as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['account_name'] }}</td>
                        <td>{{ $item['route_name'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['company_name'] }}</td>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ $item['phone'] }}</td>
                        <td>{{ $item['address1'] }}</td>
                        <td>{{ $item['frequency_day'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
   