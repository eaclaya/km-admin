<table class="table">
                                <thead>
                                        <tr>
                                                <td>Fecha Creacion</td>
                                                <td>Fecha Actualizacion</td>
                                                <td>Tipo</td>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                <td>Cantidad Anterior</td>
                                                <td>Cantidad Actualizada</td>
                                                <td>Cantidad Final</td>
                                                <td>Costo Anterior</td>
                                                <td>Costo Final</td>
                                                <td>Precio Anterior</td>
                                                <td>Precio Final</td>
                                                <td>Precio Mayorista Anterior</td>
                                                <td>Precio Mayorista Final</td>
                                                <td>Precio Especial Anterior</td>
                                                <td>Precio Especial Final</td>
                                                <td>Razon</td>
                                                <td>Comentario</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{ $item['created_at'] }}</td>
                                                <td>{{ $item['updated_at'] }}</td>
                                                <td>{{ $item['type'] }}</td>
                                                <td>{{ $item['product_key'] }}</td>
                                                <td>{{ $item['notes'] }}</td>
                                                <td>{{ $item['qty_before'] }}</td>
                                                <td>{{ $item['qty'] }}</td>
                                                <td>{{ $item['qty_after'] }}</td>
                                                <td>{{ $item['cost_before'] }}</td>
                                                <td>{{ $item['cost'] }}</td>
                                                <td>{{ $item['price_before'] }}</td>
                                                <td>{{ $item['price'] }}</td>
                                                <td>{{ $item['wholesale_price_before'] }}</td>
                                                <td>{{ $item['wholesale_price'] }}</td>
                                                <td>{{ $item['special_price_before'] }}</td>
                                                <td>{{ $item['special_price'] }}</td>
                                                <td>{{ $item['comment'] }}</td>
                                                <td>{{ $item['reason'] }}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
