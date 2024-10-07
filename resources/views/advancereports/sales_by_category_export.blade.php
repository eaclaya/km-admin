<table class="table">
                                <thead>
                                        <tr>
                                                <td>Categoria</td>
                                                <td>Unidades vendidas</td>
                                                <td>Monto vendido</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['category']}}</td>
                                                <td>{{$item['qty']}}</td>
                                                <td>{{$item['total']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
