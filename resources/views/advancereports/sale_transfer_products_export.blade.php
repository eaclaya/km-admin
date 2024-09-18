<table class="table">
      <thead>
              <tr>
                      <td>Codigo</td>
                      <td>Producto</td>
                      <td>Cantidad Vendida</td>
                      <td>Cantidad Transferida</td>
                      <td>Cantidad Total</td>
                      <td>Disponible en Tienda</td>
                      <td>Equivalencias en Tienda</td>
                      <td>Equivalencias Globales</td>
                      <td>Ventas Globales</td>
                      <td>Ventas Globales Equivalencias</td>
                      <td>Existencias Globales</td>
                      <td>Existencias en Bodega</td>
                      <td>Equivalencias Bodega</td>
                      <td>Ubicacion</td>
                      <td>Equivalencias</td>
                      <td>Proveedor</td>
                      <td>Categoria</td>
                      <td>Sub Categoria</td>
                      <td>Rotacion</td>
              </td>
      </thead>
      <tbody>
              @foreach($result as $item)
              <tr>
                      <td>{{$item['product_key']}}</td>
                      <td>{{$item['notes']}}</td>
                      <td>{{$item['sale_qty']}}</td>
                      <td>{{$item['transfer_qty']}}</td>
                      <td>{{$item['total_qty']}}</td>
                      <td>{{$item['qty']}}</td>
                      <td>{{$item['qty_related']}}</td>
                      <td>{{$item['relation_qty_global']}}</td>
                      <td>{{$item['sale_qty_global']}}</td>
                      <td>{{$item['qty_sales_relation']}}</td>
                      <td>{{$item['qty_global']}}</td>
                      <td>{{$item['qty_global_warehouse']}}</td>
                      <td>{{$item['relation_qty_warehouse']}}</td>
                      <td>{{$item['location']}}</td>
                      <td>{{$item['relation_id']}}</td>
                      <td>{{$item['vendor']}}</td>
                      <td>{{$item['category_name']}}</td>
                      <td>{{$item['sub_category_name']}}</td>
                      <td>{{$item['rotacion_name']}}</td>
              </tr>
              @endforeach
      </tbody>
</table>
