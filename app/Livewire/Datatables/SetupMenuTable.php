<?php

namespace App\Livewire\Datatables;

use App\Models\SetupMenu;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class SetupMenuTable extends DataTableComponent
{
    protected $model = SetupMenu::class;
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('supra_menu_id')
                ->options([
                    ' ' => 'All',
                    'yes' => 'Yes',
                    'no' => 'No',
                ])->filter(function(Builder $builder, string $value) {
                    $builder->where('supra_menu_id', 'like', '%'.$value.'%');
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Menu superior", "supra_menu_id"),
            Column::make("Label", "label")
                ->searchable(),
            Column::make("Label color", "label_color")
                ->searchable(),
            Column::make("Url", "url")
                ->searchable(),
            Column::make("Text", "text")
                ->searchable(),
            Column::make("Icon", "icon")
                ->searchable(),
            Column::make("Created at", "created_at")
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->searchable(),
        ];
    }
}
