<table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Tienda</td>
                                                <td>Descripcion</td>
                                                <td>Cantidad</td>
                                                <td>Costo</td>
                                                <td>Precio final</td>
                                                <td>Precio mayorista</td>
                                                <td>Proveedor</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->product_key}}</td>
                                                <td>{{$item->account}}</td>
                                                <td>{{$item->notes}}</td>
                                                <td>{{$item->qty}}</td>
                                                <td>{{$item->cost}}</td>
                                                <td>{{$item->price}}</td>
                                                <td>{{$item->wholesale_price}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
