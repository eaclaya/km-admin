<?php

namespace App\Livewire\Datatables;

use App\Models\Main\TrackingHistory;
use \Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Livewire\Attributes\On;

class TrackingTable extends DataTableComponent
{
    public int $id;
    public string $current_model;

    public function builder(): Builder
    {
        $trackingHistory = TrackingHistory::orderBy('id', 'DESC');

        if($this->current_model){
            $trackingHistory = $trackingHistory->where('model', $this->current_model);
        }
        if($this->id){
            $trackingHistory = $trackingHistory->where('model_id', $this->id);
        }
        return $trackingHistory;
    }
    public function configure(): void
    {
        $this->setSearchDebounce(1000);
        $this->setShouldRetrieveTotalItemCountDisabled();
        $this->setPaginationMethod('simple');
        $this->setPerPageVisibilityDisabled();
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        $columns = [
            Column::make("Usuario", "user_id")
                ->format(function(string $value, $row) {
                    return isset($row->user) ? $row->user->name : 'error al guardar';
                })->html(),
            Column::make("Usuario Real", "real_user_id")
                ->format(function(string $value, $row) {
                    return isset($row->realUser) ? $row->realUser->name : 'error al guardar';
                })->html(),
            Column::make("Modelo", "model")
                ->format(function($value, $row) {
                    return isset($row->currentModel($row->model)->name) ? $row->currentModel($row->model)->name : $row->model;
                })->html(),
            Column::make("Motivo", "reason"),
            Column::make("Antes", "before_data")
                ->format(function($value, $row) {
                    return $this->getData($row->before_data, $row);
                })->html(),
            Column::make("Despues", "after_data")
                ->format(function($value, $row) {
                    return $this->getData($row->after_data, $row);
                })->html(),
            Column::make("Fecha", "created_at"),
        ];
        return $columns;
    }

    public function getData($data, $item)
    {
        // dd($item->currentModel($item->model));
        $table = '<table> ';
        foreach($data as $key => $value){
            $table .= '<tr style="border: 3px solid black !important;" ><td >'.$key.'</td>';
            $j = 1;
            if(isset($data[$key]) && is_array($data[$key])){
                $value = '';
                foreach($data[$key] as $v){
                    $comma = ($j == count($data[$key])) ? '' : ', ';
                    $method = 'get_'.$key;
                    if (method_exists($item->currentModel($item->model), $method)) {
                        $v = $item->currentModel($item->model)->$method($v);
                    } else {
                        $v = isset($v) ? $v : '';
                    }
                    $value .= $v.$comma;
                    $j++;
                }
                $table .= '<td >'.$value.'</td></tr>';
            }elseif(isset($data[$key])){
                $method = 'get_'.$key;
                if (method_exists($item->currentModel($item->model), $method)) {
                    $value = $item->currentModel($item->model)->$method($data[$key]);
                } else {
                    $value = isset($data[$key]) ? $data[$key] : '';
                }
                $table .= '<td >'.$value.'</td></tr>';
            }else{
                if(isset($value) && is_array($value)){
                    $j = 1;
                    foreach($value as $v){
                        $comma = ($j == count($value)) ? '' : ', ';
                        $method = 'get_'.$key;
                        if (method_exists($item->currentModel($item->model), $method)) {
                            $v = $item->currentModel($item->model)->$method($v);
                        } else {
                            $v = isset($v) ? $v : '';
                        }
                        $val .= $v.$comma;
                        $j++;
                    }
                    $table .= '<td >'.$val.'</td></tr>';
                }else{
                    $method = 'get_'.$key;
                    if (method_exists($item->currentModel($item->model), $method)) {
                        $value = isset($value) ? $item->currentModel($item->model)->$method($value) : '';
                    } else {
                        $value = isset($value) ? $value : '';
                    }
                    $table .= '<td >'.$value.'</td></tr>';
                }
            }
        }
        $table .= '</table>';
        return $table;
    }

    #[On('reload-data-tracking-table')]
    public function reloadData($current_model, $id): void
    {
        $this->current_model = $current_model;
        $this->id = $id;
        // $this->mount();
        $this->dispatch('refreshDatatable');
    }
}
