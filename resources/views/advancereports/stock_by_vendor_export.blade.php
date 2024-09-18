 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                <td>Proveedor</td>
                                                @if(reset($result)['is_root'])
                                                <td>Costo</td>
                                                @endif
                                                <td>Precio Final</td>
                                                <td>Precio Mayorista</td>
                                                <td>Precio Especial</td>
                                                <td>Unidades</td>
                                                <td>Tiendas</td>
                                                <td>Fecha Actualizacion</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['vendor']}}</td>
                                                @if($item['is_root'])
                                                <td>{{$item['cost']}}</td>
                                                @endif
                                                <td>{{$item['price']}}</td>
                                                <td>{{$item['wholesale_price']}}</td>
                                                <td>{{$item['special_price']}}</td>
                                                <td>{{$item['qty']}}</td>
                                                <td>{{implode(' , ', $item['accounts'])}}</td>
                                                <td>{{$item['updated_at']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
