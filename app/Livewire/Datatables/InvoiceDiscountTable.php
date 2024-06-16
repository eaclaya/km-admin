<?php

namespace App\Livewire\Datatables;

use App\Models\CloningControl;
use App\Models\InvoiceDiscount;
use App\Models\Main\Account;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class InvoiceDiscountTable extends DataTableComponent
{
    public ?int $account_id;

    public array $bulkActions = [
        'exportSelected' => 'Imprimir',
    ];

    public function exportSelected()
    {
        dd($this->getSelected());
        return redirect()->route('invoice_discount.export_invoice', ['ids' => $this->selectedKeys]);
    }
    public function builder(): Builder
    {
        $invoicesDiscount = InvoiceDiscount::on('mysql');
        if(isset($this->account_id)){
            $invoicesDiscount = $invoicesDiscount->where('account_id', $this->account_id);
        }

        return $invoicesDiscount->select([
            'invoice_id',
            'account_id',
            'invoice_number',
            'invoice_date',
            'amount',
            'created_at'
        ]);
    }
    public function configure(): void
    {
        $this->setPrimaryKey('invoice_id');
        $this->setFilterLayoutSlideDown();
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Desde')
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('invoice_date', '>=', $value);
                }),
            DateFilter::make('Hasta')
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('invoice_date', '<=', $value);
                }),
        ];
    }
    public function columns(): array
    {
        return [
            Column::make("Id de Factura", "invoice_id")
                ->searchable(),
            Column::make("Tienda", "account_id")
                ->format(function(string $value, $row) {
                    return '<a href="'.route("invoice_discount.index",["account_id"=> $row->account_id]).'">'.$row->account_name.'</a>';
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->take(10);
                    return $query->orWhereIn('account_id', $accounts->pluck('id'));
                }),
            Column::make("Numero de factura", "invoice_number")
                ->searchable(),
            Column::make("Fecha de Facturacion", "invoice_date")
                ->searchable(),
            Column::make("Monto", "amount")
                ->searchable(),
            Column::make("Created at", "created_at")
                ->searchable(),
        ];
    }
}
