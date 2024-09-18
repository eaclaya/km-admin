<table class="table">
  <thead>
    <tr>
      <td>Codigo</td>
      <td>Producto</td>
      <td>Relacion</td>
      <td>Proveedor</td>
      <td>Cliente</td>
      <td>Factura</td>
      <td>Vendedor</td>
      <td>Tipo de pago</td>
      <td>Costo</td>
      <td>Precio</td>
      <td>Cantidad Global</td>
      <td>Cantidad en Bodega</td>
      <td>Equivalencias en Bodega</td>
      <td>Equivalencias Globales</td>
      <td>Ventas Equivalencias</td>
      <td>Unidades Vendidas</td>
      <td>Unidades Devueltas</td>
      <td>Porcentaje ult. 6 meses</td>
      <td>Porcentaje Equivalencias ult. 6 meses</td>
      <td>Costo Total</td>
      <td>Monto Total</td>
      <td>Monto Reembolsado</td>
      <td>Monto Final</td>
      <td>Tienda</td>
    </tr>
  </thead>
  <tbody>
    @foreach($result as $item)
    <tr>
      <td>{{$item['product_key']}}</td>
      <td>{{$item['notes']}}</td>
      <td>{{$item['relation_id']}}</td>
      <td>{{$item['vendor']}}</td>
      <td>{{$item['client']}}</td>
      <td>{{$item['invoice_number']}}</td>
      <td>{{$item['seller']}}</td>
      <td>{{$item['payment_type']}}</td>
      <td>{{$item['cost']}}</td>
      <td>{{$item['price']}}</td>
      <td>{{$item['availableQty']}}</td>
      <td>{{$item['qtyInWarehouse']}}</td>
      <td>{{$item['relation_qty']}}</td>
      <td>{{$item['relation_qty_global']}}</td>
      <td>{{$item['relation_sales']}}</td>
      <td>{{$item['qty']}}</td>
      <td>{{$item['qty_refunded']}}</td>
      <td>{{$item['qty_sales_products']}}</td>
      <td>{{$item['qty_sales_related']}}</td>
      <td>{{$item['total_cost']}}</td>
      <td>{{$item['total']}}</td>
      <td>{{$item['total_refunded']}}</td>
      <td>{{$item['total'] - $item['total_refunded']}}</td>
      <td>{{implode(',', $item['accounts'])}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
