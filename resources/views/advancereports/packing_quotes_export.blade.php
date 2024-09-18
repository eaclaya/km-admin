 <table class="table">
    <thead>
            <tr>
                    <td>Codigo</td>
                    <td>Descripcion</td>
                    <td>Precio Mayorista</td>
                    <td>Disponibilidad</td>
                    <td>Cantidad Original</td>
                    <td>Cantidad Final</td>
                    <td>Diferencia</td>
                    <td>Cantidad Equivalencias</td>
                    <td>Cotizacion</td>
                    <td>Factura</td>
                    <td>Cliente</td>
                    <td>Vendedor</td>
                    <td>Fecha de factura</td>
                    <td>Equivalencias</td>
                    <td>Proveedor</td>
                    <td>Cantidad Global de Productos</td>
                    <td>Cantidad Global de Equivalencias</td>
            </tr>
    </thead>
    <tbody>
    @foreach($result as $item)
    <tr>
        <td>{{$item->product_key}}</td>
        <td>{{$item->notes}}</td>
        <td>{{$item->wholesale_price}}</td>
        <td>{{$item->product_qty}}</td>
        <td>{{$item->original_qty}}</td>
        <td>{{$item->packing_qty}}</td>
        <td>{{$item->packing_qty - $item->original_qty}}</td>
        <td>{{$item->relation_qty}}</td>
        @if($item->group == false)
            <td>{{$item->invoice_number}}</td>
            <td>{{$item->final_invoice_number}}</td>
            <td>{{$item->client_name}}</td>
            <td>{{$item->employee_name}}</td>
            <td>{{$item->invoice_date}}</td>
        @else
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
            <td>N/A</td>
        @endif
        <td>{{$item->relation_id}}</td>
        <td>{{$item->vendor}}</td>

        <td>{{$item->qty_global_total}}</td>
        <td>{{$item->qty_global_related}}</td>
    </tr>
    @endforeach
    </tbody>
</table>
