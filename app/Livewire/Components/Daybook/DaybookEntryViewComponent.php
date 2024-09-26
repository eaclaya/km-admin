<?php

namespace App\Livewire\Components\daybook;

use App\Models\FinanceDaybookEntry;
use Livewire\Attributes\On;
use Livewire\Component;

class DaybookEntryViewComponent extends Component
{
    public $primaries;
    public $secondaries;
    public function mount($primaries = null, $secondaries = null)
    {
        $this->primaries = $primaries;
        $this->secondaries = $secondaries;
    }

    public function render()
    {
        return view('livewire.components.daybook.daybook-entry-view');
    }

    #[On('daybook-entry-view-reload')]
    public function daybookReload($id){
        $items = FinanceDaybookEntry::find($id)->items;
        [$secondaries, $primaries] = $items->partition(function (&$item) {
            $item->catalogue_number = isset($item->catalogueItem) ? $item->catalogueItem->number : 'error de catalogo';
            return $item->is_primary > 2;
        });

        $secondaries = $secondaries->groupBy('is_primary')->toArray();
        $primaries = $primaries->groupBy('is_primary')->toArray();

        $this->mount($primaries, $secondaries);
    }
}
