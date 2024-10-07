  @foreach($result as $client)
        <div class="row">
                <div class="col-md-12">
                        <h4>{{$client['name']}} - L. {{number_format($client['balance'], 2, '.', ',')}} - {{$client['route_name']}}</h4>
                </div>
        </div>
        <div class="row">
                <table class="table table-bordered">
			<thead>
			    <tr>
                                <td>Factura</td>
                                <td>Fecha de Factura</td>
                                <td>Dias Credito</td>
                                <td>Fecha de Vencimiento</td>
                                <td>Dias Faltantes</td>
                                <td>Dias Vencidos</td>
                                <td>Monto Factura</td>
                                <td>Abonado</td>
				<td>Pendiente</td>
			     </tr>
                        </thead>
                        <tbody>
                        @foreach($client['invoices'] as $invoice)
                                 <tr>
                                <td>{{$invoice->invoice_number}}</td>
                                <td>{{$invoice->invoice_date}}</td>
                                <td>{{$invoice->credit_days}}</td>
                                <td>{{$invoice->end_date}}</td>
                                <td>{{$invoice->datediff2}}</td>
                                <td>{{$invoice->datediff3}}</td>
                                <td>{{$invoice->amount}}</td>
                                <td>{{$invoice->amount - $invoice->balance}}</td>
                                <td>{{$invoice->balance}}</td>
                                </tr>
                        @endforeach
                        </tbody>
                </table>
        </div>
        <br><br>
        @endforeach
