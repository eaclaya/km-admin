@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.expenses_by_category') }}">
                <div class="col-md-3">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" />
                </div>
                <div class="col-md-3">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" />
                </div>
                <div class="col-md-2">
                    <p>Tienda</p>
                    <select id="store" name="store" class="control-form form-control">
                        <option value="0">Todas</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
        </div>
        <div class="col-md-6">
            <p>&nbsp;</p>
            <select name="export" class="select-group control-form form-control">
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
						<td>Fecha</td>
						<td>Tienda</td>
						<td>Categoria</td>
                        <td>Subcategoria</td>
                        <td>Monto</td>
					</tr>
				</thead>
				<tbody>
					@foreach($result as $item)
					<tr>
						<td>{{$item['expense_date']}}</td>
						<td>{{$item['account_name']}}</td>
						<td>{{$item['category_name']}}</td>
                        <td>{{$item['subcategory_name']}}</td>
                        <td>{{$item['amount']}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@endif

    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });
        });
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
