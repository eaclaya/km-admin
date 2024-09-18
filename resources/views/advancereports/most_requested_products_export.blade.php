<table class="table">
        <thead>
                <tr>
                        <td>Codigo</td>
                        <td>Producto</td>
                        <td>Cantidad de Pedidos</td>
                        <td>Unidades Pedidas Total</td>
                        <td>Disponible en Bodega</td>
                        <td>Fecha</td>
                        <td>Equivalencias</td>
                        <td>Proveedor</td>
                        <td>Equivalencias Globales</td>
                        <td>Equivalencias Bodega</td>
                        <td>Ventas Globales</td>
                        <td>Existencias Globales</td>
                </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item['product_key']}}</td>
                        <td>{{$item['notes']}}</td>
                        <td>{{$item['count']}}</td>
                        <td>{{$item['quantity']}}</td>
                        <td>{{$item['available']}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>{{$item['relation_id']}}</td>
                        <td>{{$item['vendor']}}</td>
                        <td>{{$item['relation_qty_global']}}</td>
                        <td>{{$item['relation_qty_warehouse']}}</td>
                        <td>{{$item['qty_sales']}}</td>
                        <td>{{$item['qty_global']}}</td>
                </tr>
                @endforeach
        </tbody>
</table>
