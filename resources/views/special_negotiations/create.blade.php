@extends("adminlte::page")

@section("title", "Crear")

@section("content_header")
    <h1>
        Crear Negociacion Especial
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{Route('special_negotiations.store')}}" method="post" multipart="multipart/form-data">
                    <div class="row">
                        @csrf
                        <div class="col-md-2">
                            <label for="route_id" class="form-label">Ruta</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Route",
                                'filters'=> ['name'],
                                'columnText'=> ['name'],
                                'name' => 'route_id',
                            ])
                        </div>
                        <div class="col-md-2">
                            <label for="account_id" class="form-label">Tienda</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Account",
                                'filters'=> ['name'],
                                'columnText'=> ['name'],
                                'name' => 'account_id',
                                'set_properties' => [
                                    [
                                        'name' => 'client_id',
                                        'filters' => [
                                            'account_id' => '$selected',
                                        ],
                                    ],
                                    [
                                        'name' => 'invoice_id',
                                        'filters' => [
                                            'account_id' => '$selected',
                                        ],
                                    ],
                                ],
                            ])
                        </div>
                        <div class="col-md-2">
                            <label for="employee_id" class="form-label">Empleado</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Employee",
                                'filters'=> ['first_name','last_name', 'id_number'],
                                'columnText'=> ['first_name','last_name'],
                                'name' => 'employee_id',
                            ])
                        </div>
                        <div class="col-md-2">
                            <label for="client_id" class="form-label">Cliente</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Client",
                                'filters'=> ['name'],
                                'columnText'=> ['name'],
                                'name' => 'client_id',
                            ])
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_id" class="form-label">Facturas</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Invoice",
                                'filters'=> ['invoice_number', 'created_at', 'amount'],
                                'columnText'=> ['invoice_number', 'created_at', 'amount'],
                                'name' => 'invoice_id',
                                'is_multiple' => true,
                            ])
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="amount" class="form-label">Monto</label>
                            <input type="number" id="amount" name="amount" step="0.01" class="form-control" readonly>
                        </div>
                        <div class="col-md-4" >
                            <label class="form-label">Seleccione una condicion:</label>
                            <div class="row p-2">
                                @foreach ($conditions as $condition)
                                    <div class="col-md-6 form-check text-justify text-nowrap" >
                                        <label class="form-check-label text-justify text-nowrap" >
                                            <input type="radio" name="conditions_special_negotiation_id"
                                                class="form-check-input" value="{{$condition->id}}"
                                                @if($loop->first) checked @endif
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
                            <input type="number" id="credit_record" name="credit_record"" step="0.01" class="form-control">
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
        let conditions = {!! json_encode($conditions) !!};
        $("#invoice_id").change(function() {
            var montoFinal = 0;
            $(this).find("option:selected").each(function() {
                var texto = $(this).text();
                var partes = texto.split("-");
                var monto = partes[partes.length - 1].trim();
                montoFinal = parseFloat(montoFinal) + parseFloat(monto);
            })
            $("#amount").val(montoFinal);
            if(montoFinal == 0){
                $("#condition_1").prop("checked", true);
            }
            for (let i = 0; i <= conditions.length; i++) {
                if(!conditions[i]){
                    continue;
                }
                const min = parseFloat(conditions[i].amount_range.min);
                const max = conditions[i].amount_range.max > 0 ? parseFloat(conditions[i].amount_range.max) : null;
                if (max == null) {
                    if (montoFinal >= min) {
                        $("#condition_" + conditions[i].id).prop("checked", true);
                    }
                }else {
                    if (montoFinal >= min && montoFinal < max) {
                        $("#condition_" + conditions[i].id).prop("checked", true);
                    }
                }
            }
        });
    </script>
@stop
