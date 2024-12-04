<?php

namespace App\Livewire\Datatables;

use App\Models\Main\Account;
use App\Models\Main\Client;
use App\Models\Main\Employee;
use App\Models\Main\Route;
use App\Models\Main\SpecialNegotiation;
use App\Services\SpecialNegotiationsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SpecialNegotiationsTable extends DataTableComponent
{
    public array $routes_id;

    public function builder(): Builder
    {
        $negotiations = SpecialNegotiation::orderBy('id', 'DESC');
        if (isset($this->routes_id)) {
            $negotiations = $negotiations->whereIn('route_id', $this->routes_id);
        }

        return $negotiations->select([
            'id',
            'route_id',
            'account_id',
            'employee_id',
            'client_id',
            'amount',
            'status',
            'is_document',
            'negotiations_discount',
        ]);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('status')) {
                if ($row->status == 0) {
                    return [
                        'class' => 'bg-success text-center',
                    ];
                } elseif ($row->status == 1) {
                    return [
                        'class' => 'bg-primary text-center',
                    ];
                } else {
                    return [
                        'class' => 'bg-danger text-center',
                    ];
                }
            }
            if ($column->isField('is_document')) {
                if ($row->is_document == 0) {
                    return [
                        'class' => 'bg-danger text-center',
                    ];
                } elseif ($row->is_document == 1) {
                    return [
                        'class' => 'bg-warning text-center',
                    ];
                } else {
                    return [
                        'class' => 'bg-success text-center',
                    ];
                }
            }

            return [];
        });
    }

    public function columns(): array
    {
        $negotiationService = App::make(SpecialNegotiationsService::class);

        return [
            Column::make('Id', 'id')
                ->format(function (string $value, $row) {
                    return '<a class="btn btn-primary btn-sm" href="'.route('special_negotiations.show', $row->id).'">'.$row->id.' - Ir</a>';
                })->html()
                ->searchable(),
            Column::make('Ruta', 'route_id')
                ->format(function (string $value, $row) {
                    return $row->route->name;
                })->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $route = Route::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(5);

                    return $query->orWhere('route_id', $route->pluck('id'));
                }),
            Column::make('Tienda', 'account_id')
                ->format(function (string $value, $row) {
                    return $row->account->name;
                })->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(5);

                    return $query->orWhereIn('account_id', $accounts->pluck('id'));
                }),
            Column::make('Empleado', 'employee_id')
                ->format(function (string $value, $row) {
                    return $row->employee->name;
                })->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $employee = Employee::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(5);

                    return $query->orWhereIn('employee_id', $employee->pluck('id'));
                }),
            Column::make('Cliente', 'client_id')
                ->format(function (string $value, $row) {
                    return $row->client->name;
                })->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $client = Client::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(5);

                    return $query->orWhereIn('client_id', $client->pluck('id'));
                }),
            Column::make('Facturas', 'client_id')
                ->format(function (string $value, $row) {
                    $invoices = $row->invoices;
                    $invoiceList = '';
                    if ($invoices->count() > 0) {
                        foreach ($invoices as $invoice) {
                            $invoiceList .= $invoice->invoice_number.' <br/>';
                        }
                    }

                    return $invoiceList;
                })->html(),
            Column::make('Monto', 'amount'),
            Column::make('Saldo Vencido', 'id')
                ->format(function (string $value, $row) use ($negotiationService) {
                    $today = Carbon::now();
                    $quota = $row->quotas()->where('credit_payment_at', '<=', $today->toDateString())->first();
                    $finalBalance = 0;
                    if (isset($quota)) {
                        $finalBalance = $negotiationService->getRepository()->calculateFinalBalance($quota->id);
                        if ($finalBalance < 0) {
                            $finalBalance = 0;
                        }
                    }

                    return $finalBalance;
                })->html(),
            Column::make('Saldo por Vencer', 'id')
                ->format(function (string $value, $row) use ($negotiationService) {
                    $quota = $row->quotas()->orderBy('id', 'DESC')->first();
                    $finalBalance = 0;
                    if (isset($quota)) {
                        $finalBalance = $negotiationService->getRepository()->calculateFinalBalance($quota->id);
                        if ($finalBalance < 0) {
                            $finalBalance = 0;
                        }
                    }

                    return $finalBalance;

                })->html(),
            Column::make('Estatus', 'status')
                ->format(function (string $value, $row) {
                    if ($row->status == 0) {
                        return 'Activo';
                    } elseif ($row->status == 1) {
                        return 'Finalizado';
                    } else {
                        return 'Vencido';
                    }
                })->html(),
            Column::make('Documentado', 'is_document')
                ->format(function (string $value, $row) {
                    if ($row->is_document == 0) {
                        return 'No';
                    } elseif ($row->is_document == 1) {
                        return 'Incompleto';
                    } else {
                        return 'Si';
                    }
                })->html(),
            Column::make('Descuento Estimado', 'estimated_percentage')
                ->format(function (string $value, $row) {
                    return $row->estimated_percentage.' %';
                })->html(),
            Column::make('Descuento Aplicado', 'negotiations_discount'),
            Column::make('Accion', 'id')
                ->format(function (string $value, $row) {
                    return '<a class="btn btn-primary btn-sm" href="'.route('special_negotiations.edit', $row->id).'">Editar</a>';
                })->html(),
        ];
    }
}
