<?php

namespace App\Livewire\Datatables;

use App\Models\Main\Account;
use App\Models\Main\UserResources;
use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Livewire\Attributes\On;

class PermissionsTable extends DataTableComponent
{
    public $category, $categories;
    public function builder(): Builder
    {
        $permissions = UserResources::on('main');
        if(isset($this->category) && $this->category != 0){
            $permissions = $permissions->where('category', $this->category);
        }else{
            $permissions = $permissions->where(function ($query){
                $query->whereNull('category')->orWhere('category', 0);
            });
        }
        return $permissions->select([
            'code',
            'name',
            'category',
            'description',
            'created_at',
            'updated_at',
            'deleted_at'
        ]);
    }

    public function changeCategory($value, $id)
    {
        DB::connection('main')->table('user_resources')->where('id', $id)->update(['category' => $value]);
        $this->dispatch('refresh-all-datatable');
    }

    public function updateValue($column, $id, $value)
    {
        $updated_at = Carbon::now();
        DB::connection('main')->table('user_resources')->where('id', $id)->update([$column => $value, 'updated_at' => $updated_at]);
    }
    #[On('refresh-all-datatable')]
    public function refreshAllDatatable()
    {
        $this->dispatch('refreshDatatable');
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id"),
            Column::make("Codigo", "code")
                ->format(function($value, $row, $column) {
                    return $this->drawnColumnInput($value, $row, $column);
                })->html()
                ->searchable(),
            Column::make("Nombre", "name")
                ->format(function($value, $row, $column) {
                    return $this->drawnColumnInput($value, $row, $column);
                })->html()
                ->searchable(),
            Column::make("Descripcion", "description")
                ->format(function($value, $row, $column) {
                    return $this->drawnColumnInput($value, $row, $column);
                })->html()
                ->searchable(),
            Column::make("Categoria", "Category")
                ->format(function($value, $row) {
                    $html = '<select class="form-control" wire:change="changeCategory($event.target.value,'.$row->id.')">';
                    foreach ($this->categories as $key => $category) {
                        $html .= '<option value="'.$key.'" '.($key == $value ? 'selected' : '').'>'.$category.'</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })->html(),

            Column::make("Created at", "created_at"),
            Column::make("Updated at", "updated_at"),
            Column::make("Deleted at", "deleted_at"),
        ];
    }

    public function drawnColumnInput($value, $row, $column) {
        return '<input class="form-control" value="'.$value.'" wire:input.debounce.500ms="updateValue('."'".trim($column->getField())."',".$row->id.',$event.target.value)">';
    }
}
