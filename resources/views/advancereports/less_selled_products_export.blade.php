 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                <td>Unidades vendidas</td>
                                                <td>Unidades en tiendas</td>
                                                <td>Unidades en bodega</td>
                                                <td>Total</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['qty']}}</td>
                                                <td>{{$item['qtyInStore']}}</td>
                                                <td>{{$item['qtyInWarehouse']}}</td>
                                                <td>{{$item['amount']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
