@extends("adminlte::page")

@section("title", "Editar")

@section("content_header")
    <h1>
        Crear
    </h1>
@stop

@section("content")
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{Route('special_negotiations.update', ['special_negotiation' => $special_negotiation->id])}}" method="post">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="account_id" class="form-label">Tienda</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Account",
                                'filters'=> ['name'],
                                'columnText'=> ['name'],
                                'name' => 'account_id',
                                'set_properties' => [
                                    [
                                        'name' => 'employee_id',
                                        'filters' => [
                                            'account_id' => '$selected',
                                        ],
                                    ],
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
                        <div class="col-md-3">
                            <label for="employee_id" class="form-label">Empleado</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Employee",
                                'filters'=> ['first_name','last_name'],
                                'columnText'=> ['first_name','last_name'],
                                'name' => 'employee_id',
                            ])
                        </div>
                        <div class="col-md-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            @livewire('components.select2-model-component', [
                                'model' => "App\\Models\\Main\\Client",
                                'filters'=> ['name'],
                                'columnText'=> ['name'],
                                'name' => 'client_id',
                            ])
                        </div>
                        <div class="col-md-3">
                            <label for="invoice_id" class="form-label">Factura</label>
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
                            <label for="status" class="form-label">Estado</label>
                            @livewire('components.select2-array-component', [
                                'array' => [
                                    "0" => 'Activo',
                                    "1" => 'Vencido',
                                ],
                                'name' => 'status',
                            ])
                        </div>
                        <div class="col-md-2">
                            <label for="is_document" class="form-label">¿Esta Documentado?</label>
                            @livewire('components.select2-array-component', [
                                'array' => [
                                    "0" => 'No',
                                    "1" => 'Si',
                                ],
                                'name' => 'is_document',
                            ])
                        </div>
                        <div class="col-md-2">
                            <label for="amount" class="form-label">Monto</label>
                            <input type="number" id="amount" name="amount" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="overdue_balance" class="form-label">Saldo vencido</label>
                            <input type="number" id="overdue_balance" name="overdue_balance" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="due_balance" class="form-label">Saldo pendiente</label>
                            <input type="number" id="due_balance" name="due_balance" step="0.01" class="form-control">
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
    {{-- Add here extra javascript --}}
@stop
