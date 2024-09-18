<table class="table">
        <thead>
            <tr>
                <td>Factura</td>
                <td>Cliente</td>
                <td>Codigo de producto</td>
                <td>Producto</td>
                <td>Costo</td>
                <td>Precio</td>
                <td>Cantidad cotizada</td>
                <td>Cantidad disponible</td>
                <td>Cantidad en Bodega</td>
                <td>Equivalencias en Bodega</td>
                <td>Equivalencias Globales</td>
                <td>Ventas Equivalencias</td>
                <td>Vendedor</td>
                <td>Perfil</td>
            </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item['id']}}</td>
                        <td>{{$item['name']}}</td>
                        <td>{{$item['product_key']}}</td>
                        <td>{{$item['product']}}</td>
                        <td>{{$item['product_cost']}}</td>
                        <td>{{$item['price']}}</td>
                        <td>{{$item['qty']}}</td>
                        <td>{{$item['product_qty']}}</td>
                        <td>{{$item['qtyInWarehouse']}}</td>
                        <td>{{$item['relation_qty']}}</td>
                        <td>{{$item['relation_qty_global']}}</td>
                        <td>{{$item['relation_sales']}}</td>
                        <td>{{$item['employee']}}</td>
                        <td>{{$item['profile']}}</td>
                </tr>
                @endforeach
        </tbody>
</table>