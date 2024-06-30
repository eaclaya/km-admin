<?php

namespace App\Livewire\Components\daybook;

use App\Models\FinanceDaybookEntry;
use Livewire\Component;

class FormCreatedDaybookComponent extends Component
{
    public ?array $daybook;
    public ?array $items;
    public function mount()
    {
        $this->daybook = [];
        $this->items = [];
    }

    public function render()
    {
        return view('livewire.components.daybook.form-created-daybook',[]);
    }

    public function save(){
        dd([
            $this->daybook,
            $this->items
        ]);
    }

}
