<table class="table">
        <thead>
                <tr>
                        <td>Codigo</td>
                        <td>Descripcion</td>
                        <td>Cantidad en Bodega</td>
                        <td>Unidades Vendidas</td>
                        <td>Monto Vendido</td>
                        <td>Categoria</td>
                        <td>Equivalencias</td>
                </tr>
        </thead>
        <tbody>
                @foreach($result as $item)
                <tr>
                        <td>{{$item['product_key']}}</td>
                        <td>{{$item['notes']}}</td>
                        <td>{{$item['qtyInWarehouse']}}</td>
                        <td>{{$item['qty']}}</td>
                        <td>{{$item['total']}}</td>
                        <td>{{$item['category']}}</td>
                        <td>{{$item['relation_id']}}</td>
                </tr>
                @endforeach
        </tbody>
</table>
