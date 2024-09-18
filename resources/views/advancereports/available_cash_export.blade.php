  @if(isset($result))
        <div class="row">
                <div class="col-md-12">
                        <table class="table">
                                <thead>
                                        <tr>
                                                <td>Tienda</td>
                                                <td>Efectivo ventas</td>
                                                <td>Caja chica</td>
                                                <td>Total efectivo</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['account']}}</td>
                                                <td>{{number_format($item['sales_cash'], 2)}}</td>
                                                <td>{{number_format($item['petty_cash'], 2)}}</td>
                                                <td>{{number_format($item['total'], 2)}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
                </div>
        </div>
        @endif
