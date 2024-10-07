@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
 	<hr> 
	<div class="row">
		<div class="col-md-12">
			<form class="filter-form" method="POST" action="{{route('advancereports.sales_by_month')}}">
              @csrf
				@if(isset($is_root)) 
				<input type="hidden" name="is_root" value="1" />
				@endif
				 <div class="col-md-3">
					<p>Fecha inicio</p>
					<input type="month" class="form-control" name="from_date" />
				</div>
				<div class="col-md-3">
					<p>Fecha fin</p>
					<input type="month" class="form-control" name="to_date" />
				</div>
				<div class="col-md-6">
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
					<td style="border: 1px solid black">Tienda</td>
					@foreach($result['columns'] as $code => $value)
						<td @if(isset($result['is_root'])) colspan="{{($code == 'total')?7:6}}" @else colspan="{{($code == 'total')?5:4}}" @endif  style="text-align: center; border: 1px solid black">{{$code}}</td>
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
						@if($code == 'total')
							<td style="background: #eee">Resultado</td>
						@endif
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
							@if(isset($item['result']))
								<td>{{$item['result']}}</td>
							@endif
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
					</tr>
					<tr>
						<td></td>
						<td colspan="2">{{$result['totals']['sale_amount']}}</td>
						<td colspan="2">{{$result['totals']['sale_cost']}}</td>
						<td>{{round(($result['totals']['sale_cost']/($result['totals']['sale_amount'] > 0 ? $result['totals']['sale_amount'] : 1) * 100), 2)}}</td>
					</tr>
				@endif
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
        } );
    </script>
@stop


