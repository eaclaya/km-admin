<?php

namespace App\Livewire\Datatables;

use App\Models\Account;
use App\Models\Roles;
use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Livewire\Attributes\On;

class RolesTable extends DataTableComponent
{

    public function builder(): Builder
    {
        $roles = Roles::on('main');
       
        return $roles->select([
            'id',
            'name',
            'created_at',
            'updated_at',
            'deleted_at'
        ]);
    }


    public function updateValue($column, $id, $value)
    {
        $updated_at = Carbon::now();
        DB::connection('main')->table('user_roles')->where('id', $id)->update([$column => $value, 'updated_at' => $updated_at]);
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

            Column::make("Nombre", "name")
                ->searchable(),
            LinkColumn::make('Acción')
                ->title(fn($row) => 'Editar')
                ->location(fn($row) => route('roles.edit', $row)),
            Column::make("Created at", "created_at"),
            Column::make("Updated at", "updated_at"),
            Column::make("Deleted at", "deleted_at"),
            
        ];
    }

}
