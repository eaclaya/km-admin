@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.invoices_deleted')}}">
              @csrf
                                <div class="col-md-3">
                                        <p>Fecha inicio eliminado</p>
                                        <input type="date" class="form-control" name="from_date" value="{{$data['from_date']??''}}" />
                                </div>
                                <div class="col-md-3">
                                        <p>Fecha fin elimindado</p>
                                        <input type="date" class="form-control" name="to_date" value="{{$data['to_date']??''}}"/>
                                </div>
								<div class="col-md-3">
									<p>Tienda</p>
									<select class="form-control" name="account_id" id="account_id">
										<option value="">Todas</option>
										@foreach($accounts as $account)
										<option @if(isset($data['account_id']) && $data['account_id'] ==  $account->id) selected @endif value="{{$account->id}}">{{$account->name}}</option>
										@endforeach
									</select>
								</div>
                                <div class="col-md-3">
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
	@if(isset($result))
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<td>Tienda</td>
						<td>Numero de factura</td>
						<td>Codigo</td>
						<td>Description</td>
						<td>Vendedor</td>
						<td>Usuario</td>
						<td>Cliente</td>
						<td>Fecha elimidado</td>
						<td>Fecha de factura</td>
						<td>Cantidad</td>
						<td>Precio Uno</td>
						<td>Cantidad en tienda</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['account']}}</td>
						<td>{{$item['invoice_number']}}</td>
						<td>{{$item['product_key']}}</td>
						<td>{{$item['notes']}}</td>
						<td>{{$item['employee']}}</td>
						<td>{{$item['user']}}</td>
						<td>{{$item['client']}}</td>
						<td>{{$item['date_deleted']}}</td>
						<td>{{$item['date']}}</td>
						<td>{{$item['qty']}}</td>
						<td>{{$item['cost']}}</td>
						<td>{{$item['qtyInhouse']}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@endif
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('table').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });

		$("#account_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop


