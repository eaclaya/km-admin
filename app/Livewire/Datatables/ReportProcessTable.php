<?php

namespace App\Livewire\Datatables;

use App\Models\CloningControl;
use App\Models\Main\Account;
use App\Models\ReportProcess;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ReportProcessTable extends DataTableComponent
{
    public string $name;
    public function builder(): Builder
    {
        $reportProcess = ReportProcess::where('report',$this->name)->orderBy('id', 'DESC')->take(30);

        return $reportProcess->select([
            'id',
            'report',
            'file',
            'status',
            'rows',
            'count_rows',
            'created_at',
            'updated_at'
        ]);
    }
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function reloadTable(): void
    {
        $this->dispatch('refreshDatatable');
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

            Column::make("Archivo", "file")
                ->format(function(string $value, $row) {
                    if ($row->status == 0) {
                        return $value;
                    }else{
                        return '<a href="'.asset($value).'">'.$value.'</a>';
                    }
                })->html()
                ->searchable(),
            LinkColumn::make('Status', 'status')
                ->title(function($row) {
                    if ($row->status == 0) {
                        return 'Marcar Finalizado';
                    } else {
                        return 'Finalizado';
                    }
                })
                ->location(function($row) {
                    return '#';
                })->attributes(function($row) {
                    if ($row->status == 0) {
                        return [
                            'class' => 'btn btn-success btn-sm',
                            'wire:click.prevent' => "finishReport($row->id)"
                        ];
                    }else{
                        return [
                            'class' => 'btn btn-success btn-sm disabled',
                            'disabled' => 'disabled',
                        ];
                    }
                }),
            Column::make("Porcentaje", "count_rows")
                ->format(function(string $value, $row) {
                    return $this->calculatePorcent($row);
                })->html(),

            Column::make("Created at", "created_at")
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->searchable(),
        ];
    }

    public function calculatePorcent($row): string
    {
        $row->count_rows = (is_null($row->count_rows) || $row->count_rows == 0) ? 0 : $row->count_rows;
        $row->rows = (is_null($row->rows) || $row->rows == 0) ? 1 : $row->rows;

        $porcentCompleting = ($row->count_rows * 100) / $row->rows;
        $porcentCompleting = round($porcentCompleting, 0);
        $porcentCompleting = ($porcentCompleting == 0 || $porcentCompleting == 1) ? 0 : ceil($porcentCompleting);
        if($porcentCompleting == 0){
            $html = 'Por Procesar';
        }else{
            $html = '<div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: '.$porcentCompleting.'%;" aria-valuenow="'.$porcentCompleting.'" aria-valuemin="0" aria-valuemax="100">'
                            .$porcentCompleting.'%
                        </div>
                    </div>';
        }
        if($porcentCompleting < 100){
            $html .= '<br>
                        <a wire:click.prevent="reloadTable" class="btn btn-success btn-sm">
                            Recargar
                            <div wire:loading wire:target="reloadTable">
                                ...
                            </div>
                        </a>';
        }
        return $html;
    }
}
