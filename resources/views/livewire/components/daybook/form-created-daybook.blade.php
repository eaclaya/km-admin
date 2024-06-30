<div>
    <form wire:submit.prevent="save" class="container">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Entrada
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" wire:model="daybook.date">
                            </div>
                            <div class="form-group col-3">
                                <label for="account">Empresa</label>
                                @livewire('components.select2-model-component', [
                                    'model' => "App\\Models\\Main\\OrganizationCompany",
                                    'filters' => ['name'],
                                    'columnText' => ['name'],
                                    'name' => 'company',
                                    'wire_model' => "daybook.company_id",
                                    'set_properties' => [
                                        [
                                            'name' => 'account',
                                            'filters' => [
                                                'organization_company_id' => '$selected',
                                                'name',
                                            ]
                                        ]
                                    ]
                                ])
                            </div>
                            <div class="form-group col-3">
                                <label for="account">Tienda</label>
                                @livewire('components.select2-model-component', [
                                    'model' => "App\\Models\\Main\\Account",
                                    'filters'=> ['name'],
                                    'columnText'=> ['name'],
                                    'name' => 'account',
                                    'wire_model' => "daybook.account_id",
                                    'set_properties' => [
                                        [
                                            'name' => 'model_id',
                                            'filters' => [
                                                'account_id' => '$selected',
                                            ],
                                        ],
                                    ],
                                ])
                            </div>
                            <div class="form-group col-3">
                                <label for="account">Modelo</label>
                                @livewire('components.select2-array-component', [
                                    'array' => [
                                        "App\\Models\\Main\\Invoice" => 'Facturas',
                                        "App\\Models\\Main\\Payment" => 'Pagos',
                                    ],
                                    'name' => 'model',
                                    'wire_model' => "daybook.model",
                                    'set_properties' => [
                                        [
                                            'name' => 'model_id',
                                            'model' => '$selected',
                                            'filters' => [
                                                'if' => [
                                                    "App\\Models\\Main\\Invoice" => ["invoice_number", "created_at", "amount"],
                                                    "App\\Models\\Main\\Payment" => ["invoice_id", "created_at", "amount"],
                                                ],
                                            ],
                                            'columnText' => [
                                                'if' => [
                                                    "App\\Models\\Main\\Invoice" => ["invoice_number", "created_at", "amount"],
                                                    "App\\Models\\Main\\Payment" => ["invoice_id", "created_at", "amount"],
                                                ],
                                            ],
                                        ],
                                    ],
                                ])
                            </div>
                            <div class="form-group col-3">
                                <label for="account">Identificar Modelo</label>
                                @livewire('components.select2-model-component', [
                                    'model' => "App\\Models\\Main\\Invoice",
                                    'filters'=> ['invoice_number', 'created_at', 'amount'],
                                    'columnText'=> ['invoice_number', 'created_at', 'amount'],
                                    'name' => 'model_id',
                                    'wire_model' => "daybook.model_id"
                                ])
                            </div>
                        </div>
                        <h3>Entrada</h3>
                        <table class="text-center mx-auto">
                            <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>parcial</th>
                                <th>debe</th>
                                <th>haber</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" class="form-control" id="description" wire:model="daybook.description"></td>
                                <td><input type="number" step="0.01" class="form-control" id="partial" wire:model="daybook.partial"></td>
                                <td><input type="number" step="0.01" class="form-control" id="debit" wire:model="daybook.debit"></td>
                                <td><input type="number" step="0.01" class="form-control" id="credit" wire:model="daybook.credit"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Items
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <h3>Items</h3>
                        <div x-data="{ rowsPrimary: [], rowsSecondary: [], newEntry: {primary: false, description: '', field: 'partial', mount: ''} }">
                            <div class="row">
                                <div class="form-group col-1">
                                    <label for="newEntry_primary">Principal</label>
                                    <input type="checkbox" class="form-control" x-model="newEntry.primary" id="newEntry_primary" value="1">
                                </div>
                                <div class="form-group col-3">
                                    <label for="newEntry_description">Descripción</label>
                                    <input class="form-control" type="text" x-model="newEntry.description" placeholder="description" id="newEntry_description">
                                </div>
                                <div class="form-group col-2">
                                    <label >Campo</label>
                                    <br>
                                    <div class="btn-group btn-group-toggle btn-group-sm col-3" data-toggle="buttons">
                                        <label class="btn btn-primary btn-sm" for="newEntry_partial">
                                            <input type="radio" value="partial" x-model="newEntry.field" name="newEntry_partial" id="newEntry_partial" autocomplete="off" checked>
                                            Parcial
                                        </label>
                                        <label class="btn btn-primary btn-sm" for="newEntry_debit">
                                            <input type="radio" value="debit" x-model="newEntry.field" name="newEntry_debit" id="newEntry_debit" autocomplete="off">
                                            Debe
                                        </label>
                                        <label class="btn btn-primary btn-sm" for="newEntry_havings">
                                            <input type="radio" value="havings" x-model="newEntry.field" name="newEntry_havings" id="newEntry_havings" autocomplete="off">
                                            Haber
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="mount">Monto</label>
                                    <input class="form-control" type="number" x-model="newEntry.mount" placeholder="Mount" id="mount">
                                </div>
                                <div class="form-group col-2">
                                    <a
                                        class="btn btn-secondary btn-sm"
                                        @click.prevent="
                                            if(newEntry.primary == 1){
                                                rowsPrimary.push({...newEntry});
                                            }else{
                                                rowsSecondary.push({...newEntry});
                                            }
                                            $wire.items.push({...newEntry});
                                            newEntry = {primary: false, description: '', field: 'partial', mount: ''}
                                        ">Add Row
                                    </a>
                                </div>
                            </div>
                            <h4>Principales</h4>
                            <table class="text-center mx-auto">
                                <thead>
                                <tr>
                                    <th>Descipcion</th>
                                    <th>Campo</th>
                                    <th>Monto</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template x-for="(row, index) in rowsPrimary" :key="index">
                                    <tr>
                                        <td x-text="row.description"></td>
                                        <td x-text="row.field"></td>
                                        <td x-text="row.mount"></td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                            <hr>
                            <h4>Extras</h4>
                            <table class="text-center mx-auto">
                                <thead>
                                <tr>
                                    <th>Descipcion</th>
                                    <th>Campo</th>
                                    <th>Monto</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template x-for="(row, index) in rowsSecondary" :key="index">
                                    <tr>
                                        <td x-text="row.description"></td>
                                        <td x-text="row.field"></td>
                                        <td x-text="row.mount"></td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <hr>
            <button class="btn btn-outline-success btn-sm mx-auto" type="submit">Save</button>
        </div>
    </form>
</div>

