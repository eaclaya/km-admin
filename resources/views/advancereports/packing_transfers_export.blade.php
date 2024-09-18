 <table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td>Disponibilidad</td>
                                                <td>Cantidad Original</td>
                                                <td>Cantidad Final</td>
                                                <td>Diferencia</td>
                                                <td>Cantidad Equivalencias</td>
						<td>Tienda</td>
						<td>Packing</td>
						<td>Transferencia</td>
						<td>Equivalencias</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->product_key}}</td>
                                                <td>{{$item->notes ? $item->notes : $item->description}}</td>
                                                <td>{{$item->product_qty}}</td>
                                                <td>{{$item->original_qty}}</td>
                                                <td>{{$item->packing_qty}}</td>
                                                <td>{{$item->packing_qty - $item->original_qty}}</td>
                                                <td>{{$item->relation_qty}}</td>
                                                @if($item->group == false)
						<td>{{$item->account_name}}</td>
						<td>{{$item->packing_id}}</td>
						<td>{{$item->transfer_id}}</td>
                                                @else
						<td>N/A</td>
						<td>N/A</td>
						<td>N/A</td>
						@endif
						<td>{{$item->relation_id}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
