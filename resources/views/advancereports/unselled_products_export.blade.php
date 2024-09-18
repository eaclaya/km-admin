 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                <td>Costo</td>
                                                <td>Precio normal</td>
                                                <td>Precio mayorista</td>
                                                <td>Precio especial</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['cost']}}</td>
                                                <td>{{$item['price']}}</td>
                                                <td>{{$item['wholesale_price']}}</td>
                                                <td>{{$item['special_price']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
