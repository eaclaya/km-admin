  <table class="table">
                                <thead>
                                        <tr>
                                                <td style="border: 1px solid black">Tienda</td>
                                                @foreach($result['columns'] as $code => $value)
                                                <td @if(isset($result['is_root'])) colspan="6" @else colspan="4" @endif  style="text-align: center; border: 1px solid black">{{$code}}</td>
                                                @endforeach
                                        </tr>
                                </thead>
                                <tbody>
                                         <tr>
                                                <td></td>
                                                @foreach($result['columns'] as $code => $values)
						<td style="background: #eee">Repuestos</td>
						<td style="background: #eee">Lubricantes Detalle</td>
                                                <td style="background: #eee">Lubricantes Mayoreo</td>
                                                <td style="background: #eee">Devoluciones</td>
                                                @if(isset($result['is_root']))
                                                <td style="background: #eee">Utilidad</td>
                                                <td style="background: #eee">% Utilidad</td>
                                                @endif

                                                @endforeach
                                        </tr>
                                        @foreach($result['values'] as $key => $items)
                                        <tr>
                                                <td>{{$key}}</td>
                                                @foreach($items as $item)
						<td>{{$item['total']}}</td>
						<td>{{$item['oil'] - $item['oil_wholesaler']}}</td>
                                                <td>{{$item['oil_wholesaler']}}</td>
                                                <td>{{$item['total_refunded']}}</td>
                                                @if(isset($result['is_root']))
                                                <td>{{$item['total_cost']}}</td>
                                                <td>{{round(($item['sale_cost']/($item['sale_amount'] > 0 ? $item['sale_amount'] : 1) * 100), 2)}}</td>
                                                @endif
                                                @endforeach
                                        </tr>
                                        @endforeach
                                        @if(isset($result['is_root']))
                                         <tr>
                                                <td></td>
                                                <td colspan="2">Venta Total</td>
                                                <td colspan="2">Utilidad Total</td>
                                                <td>% Utilidad</td>
                                        <tr>
                                        <tr>
                                                <td></td>
                                                <td colspan="2">{{$result['totals']['sale_amount']}}</td>
                                                <td colspan="2">{{$result['totals']['sale_cost']}}</td>
                                                <td>{{round(($result['totals']['sale_cost']/($result['totals']['sale_amount'] > 0 ? $result['totals']['sale_amount'] : 1) * 100), 2)}}</td>
                                        <tr>
                                        @endif
                                </tbody>
			</table>
