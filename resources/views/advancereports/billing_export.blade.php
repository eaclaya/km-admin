<table class="table">
                                <thead>
                                        <tr>
                                                <td>Tienda</td>
                                                <td>CAI</td>
                                                <td>Desde</td>
                                                <td>Hasta</td>
                                                <td>Fecha Limite</td>
                                                <td>Siguiente Factura</td>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($result as $item)
                                        <tr>
                                                <td>{{$item['account']}}</td>
                                                <td>{{$item['cai']}}</td>
                                                <td>{{$item['from_invoice']}}</td>
                                                <td>{{$item['to_invoice']}}</td>
                                                <td>{{$item['limit_date']}}</td>
                                                <td>{{$item['next_invoice']}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                        </table>
