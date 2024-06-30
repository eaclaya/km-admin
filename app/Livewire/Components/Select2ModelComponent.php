<?php

namespace App\Livewire\Components;

use App\Models\Main\Account;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Select2ModelComponent extends Component
{
    use WithPagination;

    public $name;
    public $model;
    public $filters;
    public $columnText;
    public $uniqId;
    public $all;
    public $wire_model;
    public $x_model;
    public $set_properties;
    public $hasProperties;

    public function mount(
        $model = null, $filters = null,
        $columnText = ['name'], $name = null,
        $all = null, $wire_model = null,
        $x_model = null, $set_properties = null
    )
    {
        $this->uniqId = uniqid();
        $this->model = $model;
        $this->filters = $filters;
        $this->name = isset($name) ? $name : 'select2-'.$this->uniqId;
        $this->columnText = $columnText;
        $this->all = (isset($all) && $all > 0) ? $all : 0;
        $this->wire_model = $wire_model;
        $this->x_model = $x_model;
        if(isset($set_properties) && is_array($set_properties) && count($set_properties) > 0 ){
            if ( $this->isArrayofArrays($set_properties)){
                $this->set_properties = $set_properties;
            }else{
                $this->set_properties = [$set_properties];
            }
            $this->hasProperties = 1;
        } else {
            $this->set_properties = $set_properties;
            $this->hasProperties = 0;
        }
    }

    public function render()
    {
        return view('livewire.components.select2-model-component');
    }

    #[On('getOptions')]
    public function getOptions($search, $page)
    {
        if(isset($this->columnText)){
            if(is_array($this->columnText)){
                if(count($this->columnText) > 1){
                    $separator = "";
                    $result = "";
                    $i = 0;
                    foreach ($this->columnText as $columnText) {
                        if ($i < 1) {
                            $separator = ", ' ', ";
                        } else {
                            $separator = ", ' - ', ";
                        }
                        $result .= $columnText . $separator;
                        $i++;
                    }
                    $result = rtrim($result, $separator);
                    $rename = "CONCAT(".$result.") as text";
                }else{
                  $rename = $this->columnText[0].' as text';
                }
            }elseif(is_string($this->columnText)){
                $rename = $this->columnText.' as text';
            }
            $selects = ['id', DB::raw($rename)];
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
        $this->dispatch('options', ['results' => $results])->self();
    }

    #[On('getProperties')]
    public function getProperties($getProperties,$properties_id): void
    {
        if(is_array($getProperties) && count($getProperties) > 0){
            foreach ($getProperties as $getProperty){
                if($getProperty['name'] == $this->name){
                    if(isset($getProperty['filters'])){
                        foreach($getProperty['filters'] as $filter => $value){
                            if($filter === 'if') {
                                [$result,$isIncorrect] = $this->isIf($value,$properties_id);
                                if($isIncorrect && is_array($isIncorrect) && count($isIncorrect) > 0){
                                    foreach($isIncorrect as $fil => $val){
                                        foreach($this->filters as $key => $value){
                                            if($value == $val && $val !== '$selected'){
                                                unset($this->filters[$key]);
                                                break;
                                            }
                                        }
                                        if(!is_int($fil)){
                                            unset($this->filters[$fil]);
                                        }
                                    }
                                }
                                if($result && is_array($result) && count($result) > 0){
                                    foreach($result as $fil => $val){
                                        if($val === '$selected'){
                                            $this->filters[$fil] = $properties_id;
                                        }else if(is_int($fil)) {
                                            $this->filters[] = $val;
                                        }else{
                                            $this->filters[$fil] = $val;
                                        }
                                    }
                                } else if ($result && is_string($result)){
                                    $this->filters[] = $result;
                                }
                            }else if($value === '$selected'){
                                $this->filters[$filter] = $properties_id;
                            }else if(is_int($filter)) {
                                $this->filters[] = $value;
                            } else {
                                $this->filters[$filter] = $value;
                            }
                        }
                        $this->filters = array_unique($this->filters);
                    }
                    if(isset($getProperty['model'])){
                        if(is_array($getProperty['model']) && isset($getProperty['model']['if'])){
                            foreach ($getProperty['model']['if'] as $if => $string){
                                if($if === $properties_id) {
                                    $this->model = $string;
                                    break;
                                }
                            }
                        }elseif($getProperty['model'] == '$selected'){
                            $this->model = $properties_id;
                        }else{
                            $this->model = $getProperty['model'];
                        }
                    }
                    if(isset($getProperty['columnText'])){
                        if(is_array($getProperty['columnText'])){
                            foreach($getProperty['columnText'] as $value => $thisColumnText){
                                if($value === 'if') {
                                    [$result,$isIncorrect] = $this->isIf($thisColumnText,$properties_id);
                                    if($isIncorrect && is_array($isIncorrect) && count($isIncorrect) > 0){
                                        foreach($isIncorrect as $fil => $val){
                                            foreach($this->columnText as $key => $value){
                                                if($value == $val && $val !== '$selected'){
                                                    unset($this->columnText[$key]);
                                                    break;
                                                }
                                            }
                                            if(!is_int($fil)){
                                                unset($this->columnText[$fil]);
                                            }
                                        }
                                    }
                                    if($result && is_array($result) && count($result) > 0){
                                        foreach($result as $val){
                                            $this->columnText[] = $val;
                                        }
                                    } else if ($result && is_string($result)){
                                        $this->columnText[] = $result;
                                    }
                                }else{
                                    $this->columnText[] = $thisColumnText;
                                }
                                $this->columnText = array_unique($this->columnText);
                            }
                        }else{
                            $this->columnText[] = $getProperty['columnText'];
                        }
                    }
                    $this->dispatch('clear-select',[])->self();
                    $this->mount(
                        $model = $this->model, $filters = $this->filters,
                        $columnText = $this->columnText, $name = $this->name,
                        $all = $this->all, $wire_model = $this->wire_model,
                        $x_model = $this->x_model,
                        $set_properties = $this->set_properties
                    );
                }
            }
        }
    }

    public function isIf($value,$properties_id): array|string|false
    {
        $isCorrect = [];
        $isIncorrect = [];
        if (is_array($value) || is_object($value)) {
            foreach($value as $if => $array){
                if($if === $properties_id) {
                    $array = is_array($array) ? $array : [$array];
                    $isCorrect = array_merge($isCorrect,$array);
                }else{
                    $array = is_array($array) ? $array : [$array];
                    $isIncorrect = array_merge($isIncorrect,$array);
                }
            }
        } else {
            return false;
        }
        return [$isCorrect,$isIncorrect];
    }

    #[On('sendProperties')]
    public function sendProperties($properties_id): void
    {
        $getProperties = $this->set_properties;
        $this->dispatch('getProperties', $getProperties, $properties_id);
    }

    function isArrayofArrays($array): bool
    {
        $filteredArray = array_filter($array, 'is_array');
        if(count($filteredArray) == count($array)) {
            return true;
        }
        return false;
    }
}
