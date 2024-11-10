@extends("adminlte::page")

@section("title", "Editar")

@section("content_header")
    <h1>
        Editar Negociacion Especial
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{Route('special_negotiations.update', $special_negotiation->id)}}" method="post" multipart="multipart/form-data">
                    <div class="row">
                        @csrf
                        @method('put')
                        <div class="col-md-2">
                            <label for="route_id" class="form-label">Ruta</label>
                            @livewire('components.select2-model-component', $route_select)
                        </div>
                        <div class="col-md-2">
                            <label for="account_id" class="form-label">Tienda</label>
                            @livewire('components.select2-model-component', $account_select)
                        </div>
                        <div class="col-md-2">
                            <label for="employee_id" class="form-label">Empleado</label>
                            @livewire('components.select2-model-component', $employee_select)
                        </div>
                        <div class="col-md-2">
                            <label for="client_id" class="form-label">Cliente</label>
                            @livewire('components.select2-model-component', $client_select)
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_id" class="form-label">Facturas</label>
                            @livewire('components.select2-model-component', $invoice_select)
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="status" class="form-label">Estado</label>
                            @livewire('components.select2-array-component', $status_select)
                        </div>
                        <div class="col-md-2">
                            <label for="is_document" class="form-label">¿Esta Documentado?</label>
                            @livewire('components.select2-array-component', $is_document_select)
                        </div>
                        <div class="col-md-2">
                            <label for="amount" class="form-label">Monto</label>
                            <input type="number" id="amount" name="amount" step="0.01" class="form-control" value="{{$special_negotiation->amount}}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="overdue_balance" class="form-label">Saldo vencido</label>
                            <input type="number" id="overdue_balance" name="overdue_balance" step="0.01" class="form-control" value="{{$special_negotiation->overdue_balance}}">
                        </div>
                        <div class="col-md-2">
                            <label for="due_balance" class="form-label">Saldo pendiente</label>
                            <input type="number" id="due_balance" name="due_balance" step="0.01" class="form-control" value="{{$special_negotiation->due_balance}}">
                        </div>
                        <div class="col-md-2">
                            <label for="reason" class="form-label">Rason del Cambio</label>
                            <input type="text" id="reason" name="reason" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <p></p>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section("css")
    {{-- Add here extra stylesheets --}}
@stop

@section("js")
    <script>
        $("#invoice_id").change(function() {
            var montoFinal = 0;
            $(this).find("option:selected").each(function() {
                var texto = $(this).text();
                var partes = texto.split("-");
                var monto = partes[partes.length - 1].trim();
                montoFinal = parseFloat(montoFinal) + parseFloat(monto);
            })
            $("#amount").val(montoFinal);
        });
    </script>
@stop
