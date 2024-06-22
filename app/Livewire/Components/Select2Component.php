<?php

namespace App\Livewire\Components;

use App\Models\Main\Account;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Select2Component extends Component
{
    use WithPagination;

    public $name;
    public $model;
    public $filters;
    public $columnText;
    public $uniqId;

    public function mount($model = null, $filters = null, $columnText = ['name'], $name = null)
    {
        $this->uniqId = uniqid();
        $this->model = $model;
        $this->filters = $filters;
        $this->name = isset($name) ? $name : 'select2-'.$this->uniqId;
        $this->columnText = $columnText;
    }

    public function render()
    {
        return view('livewire.components.select2-component');
    }

    public function rendered($view, $html)
    {
        $this->dispatch('render'.$this->uniqId);
    }

    #[On('getOptions')]
    public function getOptions($search, $page)
    {
        if(isset($this->columnText)){
            if(is_array($this->columnText)){
                if(count($this->columnText) > 1){
                    $separator = "";
                    $result = "";
                    for ($i = 0; $i < count($this->columnText); $i++) {
                        if ($i < 1) {
                            $separator = ", ' ', ";
                        } else {
                            $separator = ", ' - ', ";
                        }
                        $result .= $this->columnText[$i] . $separator;
                    }
                    $result = rtrim($result, $separator);
                    $rename = "CONCAT(".$result.") as text";
                }else{
                  $rename = $this->columnText[0].' as text';
                }

                $selects = ['id', DB::raw($rename)];
            }elseif(is_string($this->columnText)){
                $rename = $this->columnText.' as text';
                $selects = ['id', $rename];
            }
        }
        $filters = $this->filters;
        $results = $this->model::select($selects)->where(function($query) use ($filters){
            if(isset($filters) && count($filters) > 0){
                foreach($filters as $filter => $value){
                    if (!is_int($filter)) {
                        $query->where($filter, $value);
                    }
                }
            }
        });
        if ($search)
        {
            $results = $results->where(function($query) use ($search,$filters){
                if(isset($filters) && count($filters) > 0){
                    foreach($filters as $filter => $value){
                        if (is_int($filter)) {
                            $query->orWhere($value, 'LIKE', "%$search%");
                        } else {
                            $query->where($filter, $value);
                        }
                    }
                }
            });
        }
        $results = $results->paginate(20,['*'],null,$page);
        $this->dispatch('options', ['results' => $results]);
    }

    public function layout()
    {
        return null;
    }
}
