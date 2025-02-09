<?php

namespace App\Livewire\Datatables;

use App\Models\CloningControl;
use App\Models\FinanceDaybookEntry;
use App\Models\InvoiceDiscount;
use App\Models\Main\Account;
use App\Models\Main\OrganizationCompany;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class DaybookTable extends DataTableComponent
{
    public ?string $type;
    public ?int $id;

    public array $bulkActions = [
        'exportSelected' => 'Imprimir',
    ];

    public function exportSelected(): void
    {
        dd($this->getSelected());
        redirect()->route('invoice_discount.export_invoice_pdf',['ids' => $this->getSelected()]);
    }
    public function builder(): Builder
    {
        $daybookEntry = FinanceDaybookEntry::on('mysql')->orderBy('created_at', 'desc');
        if(isset($this->type) && $this->type == 'company'){
            $daybookEntry = $daybookEntry->where('organization_company_id', $this->id);
        }elseif(isset($this->type) && $this->type == 'account'){
            $daybookEntry = $daybookEntry->where('account_id', $this->id);
        }
        return $daybookEntry->select([
            'id',
            'created_at',
            'organization_company_id',
            'account_id',
            'description',
            'partial',
            'debit',
            'havings'
        ]);
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setFilterLayoutSlideDown();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Fecha", "Created_at")
                ->searchable(),
            Column::make("Empresa", "organization_company_id")
                ->format(function(string $value, $row) {
                    return '<a href="'.route("finance_daybook.index",["type"=> 'company',"id"=> $row->organization_company_id]).'">'.$this->splitWords($row->company_name).'</a>';
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = OrganizationCompany::on('main')->where('name', 'like', '%'.$searchTerm.'%')->take(10);
                    return $query->orWhereIn('organization_company_id', $accounts->pluck('id'));
                }),
            Column::make("Tienda", "account_id")
                ->format(function(string $value, $row) {
                    return '<a href="'.route("finance_daybook.index",["type"=> 'account',"id"=> $row->account_id]).'">'.$this->splitWords($row->account_name).'</a>';
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->take(10);
                    return $query->orWhereIn('account_id', $accounts->pluck('id'));
                }),
            Column::make("Descripcion", "description")
                ->format(function(string $value, $row) {
                    return '<a
                                class="btn btn-primary btn-sm"
                                data-toggle="modal" data-target="#myModal"
                                x-data x-on:click="$dispatch(`daybook-entry-view-reload`, {id: '.$row->id.'})"
                            >'.$this->splitWords($value).'</a>';
                })->html()
                ->searchable(),

            Column::make("parcial", "partial")
                ->searchable(),
            Column::make("debe", "debit")
                ->searchable(),
            Column::make("haber", "havings")
                ->searchable(),
        ];
    }

    public function splitWords($text, $everyNWords = 2): string
    {
        $words = str_word_count($text, 1);
        $segments = array_chunk($words, $everyNWords);
        $result = '';
        foreach ($segments as $segment) {
            $result .= implode(' ', $segment) . '</br>';
        }
        return trim($result);
    }
}
