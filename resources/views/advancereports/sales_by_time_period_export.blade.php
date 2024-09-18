<table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td>Cantidad ingreso</td>
                                                <td>Cantidad vendida</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                <td>{{$item['quantityIn']}}</td>
                                                <td>{{$item['quantityOut']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
