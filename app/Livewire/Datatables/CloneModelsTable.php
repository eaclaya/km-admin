<?php

namespace App\Livewire\Datatables;

use App\Models\CloningControl;
use App\Models\Main\Account;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class CloneModelsTable extends DataTableComponent
{
    public $model, $account_id, $notIsCompleted;
    public function builder(): Builder
    {
        $cloningControl = CloningControl::on('mysql')
            ->where('model', $this->model);
        if($this->account_id){
            $cloningControl = $cloningControl->where('account_id', $this->account_id);
        }
        if($this->notIsCompleted){
            $cloningControl = $cloningControl->where('is_completed', 0);
        }
        return $cloningControl->select([
            'model',
            'account_id',
            'from_date',
            'to_date',
            'is_completed',
            'created_at',
            'updated_at'
        ]);
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    /*public function filters(): array
    {
        return [
            CloningControl::make('supra_menu_id')
                ->options([
                    ' ' => 'All',
                    'yes' => 'Yes',
                    'no' => 'No',
                ])->filter(function(Builder $builder, string $value) {
                    $builder->where('supra_menu_id', 'like', '%'.$value.'%');
                }),
        ];
    }*/

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable(),

            Column::make("Tienda", "account_id")
                ->format(function(string $value, $row) {
                    return '<a href="'.route("clone_models.list",["model"=> $this->model, "model_id"=> $row->account_id]).'">'.$row->account_name.'</a>';
                })->html()
                ->searchable(function(Builder $query, $searchTerm) {
                    $accounts = Account::on('main')->where('name', 'like', '%'.$searchTerm.'%')->take(10);
                    return $query->orWhereIn('account_id', $accounts->pluck('id'));
                }),

            Column::make("Desde", "from_date")
                ->searchable(),
            Column::make("Hasta", "to_date")
                ->searchable(),
            LinkColumn::make('Completado', 'is_completed')
                ->title(function($row) {
                    if ($row->is_completed) {
                        return 'SI';
                    }
                    else {
                        return 'Completar';
                    }
                })
                ->location(function($row) {
                    if ($row->is_completed) {
                        return '#';
                    }else{
                        return route('clone_models.complete',['clone_id'=> $row->id]);
                    }
                })->attributes(function($row) {
                    if ($row->is_completed) {
                        return [
                            'class' => 'btn btn-success btn-sm disabled',
                            'disabled' => 'disabled'
                        ];
                    }else{
                        return [
                            'class' => 'btn btn-success btn-sm'
                        ];
                    }
                }),
            Column::make("Created at", "created_at")
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->searchable(),
        ];
    }
}
