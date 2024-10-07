<table class="table">
    <thead>
        <tr>
        <td>Tienda</td>
        <td>Numero de factura</td>
        <td>Codigo</td>
        <td>Description</td>
        <td>Vendedor</td>
        <td>Usuario</td>
        <td>Cliente</td>
        <td>Fecha elimidado</td>
        <td>Fecha de factura</td>
        <td>Cantidad</td>
        <td>Precio Uno</td>
        <td>Cantidad en tienda</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($result as $item)
            <tr>
                <td>{{ $item['account'] }}</td>
                <td>{{ $item['invoice_number'] }}</td>
                <td>{{ $item['product_key'] }}</td>
                <td>{{ $item['notes'] }}</td>
                <td>{{ $item['employee'] }}</td>
                <td>{{ $item['user'] }}</td>
                <td>{{ $item['client'] }}</td>
                <td>{{ $item['date_deleted'] }}</td>
                <td>{{ $item['date'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['cost'] }}</td>
                <td>{{ $item['qtyInhouse'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
