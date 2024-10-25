<?php

namespace App\Livewire\Datatables;

use App\Models\Main\SpecialNegotiation;

use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class SpecialNegotiationsTable extends DataTableComponent
{
    public function builder(): Builder
    {
        $reportProcess = SpecialNegotiation::orderBy('id', 'DESC')->take(30);

        return $reportProcess->select([
            'id',
            'account_id',
            'employee_id',
            'client_id',
            'invoice_id',
            'amount',
            'overdue_balance',
            'due_balance',
            'status',
            'is_document',
            'negotiations_discount'
        ]);
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function finishReport($id): void
    {
        $reportProcess = ReportProcess::find($id);
        $reportProcess->status = 1;
        $reportProcess->save();
        $this->reloadTable();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Tienda", "account_id")
                ->format(function(string $value, $row) {
                    return $row->account->name;
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(10);
                    return $query->orWhereIn('products.account_id', $accounts->pluck('id'));
                }),
            Column::make("Empleado", "employee_id")
                ->format(function(string $value, $row) {
                    return $row->employee->name;
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $employee = Employee::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(10);
                    return $query->orWhereIn('products.account_id', $employee->pluck('id'));
                }),
            Column::make("Cliente", "client_id")
                ->format(function(string $value, $row) {
                    return $row->client->name;
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $client = Client::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(10);
                    return $query->orWhereIn('products.account_id', $client->pluck('id'));
                }),
            Column::make("Factura", "invoice_id")
                ->format(function(string $value, $row) {
                    return $row->invoice->invoice_number;
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $invoice = Invoice::on('main')->where('name', 'like', '%'.$searchTerm.'%')->select('id')->take(10);
                    return $query->orWhereIn('products.account_id', $invoice->pluck('id'));
                }),
            Column::make("Monto", "amount"),
            Column::make("Saldo Vencido", "overdue_balance"),
            Column::make("Saldo por Vencer", "due_balance"),
            Column::make('Estatus', 'status')
                ->format(function(string $value, $row) {
                    if ($row->status == 0) {
                        return 'Activo';
                    } else {
                        return 'Vencido';
                    }
                })->html()
                ->attributes(function($row) {
                    if ($row->status == 0) {
                        return [
                            'class' => 'bg-success',
                        ];
                    }else{
                        return [
                            'class' => 'bg-danger',
                        ];
                    }
                }),
            Column::make('Documentado', 'is_document')
                ->format(function(string $value, $row) {
                    if ($row->status == 0) {
                        return 'No';
                    } else {
                        return 'Si';
                    }
                })->html()
                ->attributes(function($row) {
                    if ($row->status == 0) {
                        return [
                            'class' => 'bg-danger',
                        ];
                    }else{
                        return [
                            'class' => 'bg-success',
                        ];
                    }
                }),
            Column::make("Descuento Aplicado", "negotiations_discount"),
        ];
    }

}
