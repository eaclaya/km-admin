@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
<style>
    table td, table th{
			
			border-bottom: 1px solid #ddd;
		}
</style>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.expenses_all_cash_count') }}">
                <div class="col-md-2">
                    <p>Fecha inicio</p>
                    <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="col-md-2">
                    <p>Fecha fin</p>
                    <input type="date" class="form-control" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
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
                
                <div class="col-md-2">
                    <p>&nbsp;</p>
                    <select name="export" class="control-form form-control">
                        <option value="0">Ver Resultados</option>
                        <option value="1">Exportar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <p>&nbsp;</p>
                    <button type="submit" class="btn btn-primary btn-block w-auto">CONTINUAR</button>
                </div>
            </form>
        </div>
    </div>
    <hr>

    @if (isset($results))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
						    <td>Tienda</td>
						    <td>Categoria</td>
						    <td>Subategoria</td>
                            <td>Monto</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $store => $result)
                            <tr>
                                <td><strong>{{ $stores[$store]->name??'' }}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($result as $categoria => $item)
                                <tr>
                                    <td></td>
                                    <td>{{ $categories[$categoria]->name??'No encontrado' }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($item as $subcategoria => $monto)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{  $subcategories_all[$subcategoria]->code??''}} - {{$subcategories_all[$subcategoria]->name??'' }}</td>
                                        <td>{{ $monto }}.00</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        /* $(document).ready(function() {
            $('table').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });
        }); */
        $("#expense_subcategory_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#expense_category_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#finance_account_id").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop
