@extends("adminlte::page")

@section("title", "index")

@section("content_header")
    <h1>Exportar Facturas</h1>
@stop

@section("content")
    <div class="container">
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form class="filter-form row" method="POST" action="{{route('invoice_discount.export_invoice') }}">
                    @csrf
                    <div class="col-md-3">
                        <p>Fecha inicio</p>
                        <input type="date" class="form-control" name="from_date" />
                    </div>
                    <div class="col-md-3">
                        <p>Fecha fin</p>
                        <input type="date" class="form-control" name="to_date" />
                    </div>
                    <div class="col-md-3">
                        <p>Tiendas</p>
                        @livewire('components.select2-model-component', $bodySelectAccount)
                    </div>
                    <div class="col-md-3">
                        <p>CAI</p>
                        <select  id="filter" name="filter" class="select-group control-form form-control">
                            <option value="all">todos</option>
                            <option value="1">Solo con CAI</option>
                            <option value="2">Solo sin CAI</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-block">CONTINUAR</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>
        <livewire:Datatables.report-process-table :name="$name" />

    </div>
    <script>

        $("#store").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
        });
    </script>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    {{-- Add here extra javascript --}}
@stop
