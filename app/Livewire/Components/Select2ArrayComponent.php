<?php

namespace App\Livewire\Components;

use App\Models\Main\Account;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Select2ArrayComponent extends Component
{
    use WithPagination;

    public $name;
    public $array;
    public $uniqId;
    public $all;
    public $wire_model;
    public $x_model;
    public $set_properties;
    public $hasProperties;
    public $is_multiple;
    public $optionSelected;

    public function mount($array = null, $name = null, $all = null, $wire_model = null, $x_model = null, $set_properties = null, $is_multiple = false, $optionSelected = null)
    {
        $this->uniqId = uniqid();
        $this->optionSelected = isset($optionSelected) ? $optionSelected : [];
        $this->array = $this->sortArray($array);
        $this->name = isset($name) ? $name : 'select2-'.$this->uniqId;
        $this->all = (isset($all) && $all > 0) ? $all : 0;
        $this->wire_model = $wire_model;
        $this->x_model = $x_model;
        $this->is_multiple = $is_multiple;
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
        return view('livewire.components.select2-array-component');
    }

    #[On('sendProperties')]
    public function sendProperties($properties_id): void
    {
        $getProperties = $this->set_properties;
        $this->dispatch('getProperties', $getProperties, $properties_id);
    }

    public function sortArray($array): array
    {
        $newArray = [];
        if(isset($array) && count($array) > 0){
            if (isset($this->all) && $this->all > 0){
                $newArray[] = ['id' => 'all', 'text' => 'Todos'];
            }
            foreach($array as $key => $value){
                if (is_array($value)){
                    if(isset($value['id']) && isset($value['text'])){
                        if ( in_array($value['id'], $this->optionSelected) ) {
                            $newArray[] = ['id' => $value['id'], 'text' => $value['text'], 'selected' => true];
                        }else{
                            $newArray[] = ['id' => $value['id'], 'text' => $value['text']];
                        }
                    }
                } else {
                    if ( in_array($key, $this->optionSelected) ) {
                        $newArray[] = ['id' => $key, 'text' => $value, 'selected' => true];
                    }else{
                        $newArray[] = ['id' => $key, 'text' => $value];
                    }
                }
            }
        }
        return $newArray;
    }

    public function isArrayofArrays($array): bool
    {
        $filteredArray = array_filter($array, 'is_array');
        if(count($filteredArray) == count($array)) {
            return true;
        }
        return false;
    }

}
