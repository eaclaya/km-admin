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
                            <label for="amount" class="form-label">Monto</label>
                            <input type="number" id="amount" name="amount" step="0.01" class="form-control" value="{{$special_negotiation->amount}}" readonly>
                        </div>
                        <div class="col-md-4" >
                            <label class="form-label">Seleccione una condicion:</label>
                            <div class="row p-2">
                                @foreach ($conditions as $condition)
                                    <div class="col-md-6 form-check text-justify text-nowrap" >
                                        <label class="form-check-label text-justify text-nowrap" >
                                            <input type="radio" name="conditions_special_negotiation_id"
                                                class="form-check-input" value="{{$condition->id}}"
                                                @if($special_negotiation->conditions_special_negotiation_id == $condition->id) checked @endif
                                                id="condition_{{$condition->id}}"
                                            />
                                            {{$condition->amount_range_string}} /
                                            {{$condition->condition_range}} /
                                            {!! $condition->discount_string !!}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="credit_record" class="form-label">Record Crediticio</label>
                            <input type="number" id="credit_record" name="credit_record"" step="0.01" class="form-control" value="{{$special_negotiation->credit_record}}" >
                        </div>
                        <div class="col-md-2">
                            <label for="reason" class="form-label">Rason del Cambio</label>
                            <input type="text" id="reason" name="reason" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <p></p>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
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
