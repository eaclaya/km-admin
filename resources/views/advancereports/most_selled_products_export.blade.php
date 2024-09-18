<table class="table">
        <thead>
                <tr>
                        <td>Codigo</td>
                        <td>Producto</td>
                        <td>Unidades vendidas</td>
                        <td>Total facturado</td>
                        <td>Unidades en tiendas</td>
                        <td>Unidades en bodega</td>
                        <td>Equivalencias en tienda</td>
                        <td>Equivalencias en bodega</td>
                        <td>Cantidad Global</td>
                        @if(in_array(Auth::user()->realUser()->id, Auth::user()->root))
                        <td>Costo</td>
                        <td>Total costo</td>
                        <td>Utilidad</td>
                        @endif
                        <td>Equivalencias</td>
                        <td>Proveedor</td>
                        <td>Categoría</td>
                        <td>Sub categoría</td>
                        <td>Rotación</td>
                </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item['product_key']}}</td>
                        <td>{{$item['notes']}}</td>
                        <td>{{$item['qty']}}</td>
                        <td>{{$item['total']}}</td>
                        <td>{{$item['qtyInStore']}}</td>
                        <td>{{$item['qtyInWarehouse']}}</td>
                        <td>{{$item['relationQtyInStore']}}</td>
                        <td>{{$item['relationQtyInWarehouse']}}</td>
                        <td>{{$item['qty_global']}}</td>
                        @if(in_array(Auth::user()->realUser()->id, Auth::user()->root))
                        <td>{{$item['cost']}}</td>
                        <td>{{$item['total_cost']}}</td>
                        <td>{{$item['total'] - $item['total_cost']}}</td>
                        @endif
                        <td>{{$item['relation_id']}}</td>
                        <td>{{$item['vendor']}}</td>
                        <td>{{$item['category_name']}}</td>
                        <td>{{$item['sub_category_name']}}</td>
                        <td>{{$item['rotacion_name']}}</td>
                </tr>
                @endforeach
        </tbody>
</table>
