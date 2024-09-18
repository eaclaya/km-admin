@extends('adminlte::page')

@section('title', 'Reporte')

@section('content_header')
    <h1>Reportes Avanzados</h1>
@stop



@section('content')
    <hr>
    <div class="row">
        <div class="col-md-12">
            <form class="filter-form" method="POST" action="{{ route('advancereports.expenses_cash_count') }}">
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
                    <p>Cuentas De Tienda</p>
                    <select id="finance_account_id" name="finance_account_id" class="control-form form-control">
                        <option value="0">Todas</option>
                        @foreach ($financeAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <p>Cuentas</p>
                    <select class="form-control" id="expense_category_id" name="expense_category_id">
                        <option value="0">Todas</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <p>Sub Cuentas</p>
                    <select class="form-control" id="expense_subcategory_id" name="expense_subcategory_id">
                        <option value="0">Todas</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}">
                                {{$subcategory->code .' '. $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p>&nbsp;</p>
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

    @if (isset($result))
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
						    <td>Hecho por</td>
                            <td>Creada</td>
                            <td>Asignada A Cierre</td>
                            <td>Tienda</td>
                            <td>Cta Tienda</td>
                            <td>Usuario</td>
                            <td>Cuenta</td>
                            <td>Subcuenta</td>
                            <td>Aprobado</td>
                            <td>Descripción</td>
                            <td>Monto</td>
                            <td>ID Cierre</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
							    <td>{{ $item->employee_name.' '.$item->employee_name2 }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->expense_date }}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>{{ $item->fa_name }}</td>
                                <td>{{ $item->user_name .' '.$item->user_name2 }}</td>
                                <td>{{ $item->categorie_name }}</td>
                                <td>{{ $item->code .' '.$item->subcategorie_name }}</td>
                                <td>{{ $item->is_approved?'si':'No'  }}</td>
                                <td>{{ $item->public_notes }}</td> 
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->cash_count_id }}</td>

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
