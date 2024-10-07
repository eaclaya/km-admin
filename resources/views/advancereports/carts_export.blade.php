 @if(isset($result))
        <div class="row">
                <div class="col-md-12">
                         <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                                                                <td>Cantidad</td>
                                                                                                <td>Tienda</td>

                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['product_key']}}</td>
                                                <td>{{$item['notes']}}</td>
                                                                                                <td>{{$item['qty']}}</td>
                                                                                                <td>{{$item['account']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
                </div>
        </div>
        @endif
