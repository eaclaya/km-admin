<table class="table">
                                <thead>
                                        <tr>
                                                <td>Codigo</td>
                                                <td>Producto</td>
                                                <td>Transferencia</td>
                                                <td>Fecha</td>
                                                <td>Origen</td>
                                                <td>Destino</td>
                                                <td>Cantidad Enviada</td>
                                                <td>Cantidad Recibida</td>
                                                <td>Sobrante/Faltante</td>
                                                <td>Comentario</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item->product_key}}</td>
                                                <td>{{$item->notes}}</td>
                                                <td>{{$item->transfer_id}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td>{{$item->from_account}}</td>
                                                <td>{{$item->to_account}}</td>
                                                <td>{{$item->qty_sent}}</td>
                                                <td>{{$item->qty_received}}</td>
                                                <td>{{$item->qty_returned}}</td>
                                                <td>{{$item->description}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
