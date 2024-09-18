@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr>
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.receivables_by_route')}}">
              @csrf
				 <div class="col-md-3">
                                        <p>Fecha inicio</p>
                                        <input type="date" class="form-control" name="from_date" />
                                </div>
                                <div class="col-md-3">
                                        <p>Fecha fin</p>
                                        <input type="date" class="form-control" name="to_date" />
				</div>
				<div class="col-md-2">
					<p>&nbsp;</p>
                                        <select id="route_name" name="route_name" class="control-form form-control">
                                                <option disabled selected>Todas</option>
                                                @foreach($routes as $route)
                                                <option value="{{$route}}">{{$route}}</option>
                                                @endforeach
                                        </select>
                                </div>
                                <div class="col-md-4">
					<p>&nbsp;</p>
                                        <select  name="export" class="select-group control-form form-control">
                                                <option value="0">Ver Resultados</option>
                                                <option value="1">Exportar</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                                </div>
			</form>
		</div>
	</div>
	<hr>
	  @foreach($clients as $client)
        <div class="row">
                <div class="col-md-12">
                        <h4>{{$client['name']}} - L. {{number_format($client['balance'], 2, '.', ',')}} - {{$client['route_name']}}</h4>
                </div>
        </div>
        <hr>
        <div class="row">
                <table class="table table-bordered">
                        <thead>
                                <td>Factura</td>
                                <td>Fecha de Factura</td>
                                <td>Dias Credito</td>
                                <td>Fecha de Vencimiento</td>
                                <td>Dias Faltantes</td>
                                <td>Dias Vencidos</td>
                                <td>Monto Factura</td>
                                <td>Abonado</td>
                                <td>Pendiente</td>
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
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        } );
        $("#route_name").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
