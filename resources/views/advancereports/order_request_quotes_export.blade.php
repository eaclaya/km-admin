 <table class="table">
        <thead>
                <tr>
                        <th>Nro de Orden</th>
                        <th>Tienda</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Comentario</th>
                        <th>Precio Mayorista</th>
                        <th>Cantidad Solicitada</th>
                        <th>Cantidad Solicitada Neta</th>
                        <th>Cantidad Tienda</th>
                        <th>Cantidad Equivalencias Tienda</th>
                        <th>Cantidad Bodega</th>
                        <th>Cantidad Equivalencias Bodega</th>
                        <th>Ubicacion Bodega</th>
                        <th>Equivalencias</th>
                        <th>Cantidad Vendida del Producto</th>
                        <th>Cantidad Vendida de Equivalencia</th>
                        <td>Proveedor</a>
                        <th>Fecha</th>
                </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item->order_id}}</td>
                        <td>{{$item->account_name}}</td>
                        <td>{{$item->product_key}}</td>
                        <td>{{$item->description}}</td>
                        <td>{{$item->comments}}</td>
                        <td>{{$item->wholesale_price}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{$item->qty_total}}</td>
                        <td>{{$item->actualQty}}</td>
                        <td>{{$item->relation_qty}}</td>
                        <td>{{$item->availableQty}}</td>
                        <td>{{$item->relation_qty_warehouse}}</td>
                        <td>{{$item->locationInWarehouse}}</td>
                        <td>{{$item->relation_id}}</td>
                        <td>{{$item->qty_product_sales}}</td>
                        <td>{{$item->qty_relation_sales}}</td>
                        <td>{{$item->vendor}}</td>
                        <td>{{$item->created_at}}</td>
                </tr>
                @endforeach
        </tbody>
</table>
