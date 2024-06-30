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
        [$secondaries, $primaries] = $items->partition(function ($item) {
            return $item->partial > 0;
        });
        if ($secondaries->isNotEmpty()) {
            $firstItem = $secondaries->first();
            $lastItem = $secondaries->last();
            $firstItem->debit = $firstItem->partial;
            $firstItem->partial = 0;
            $lastItem->havings = $lastItem->partial;
            $lastItem->partial = 0;
        }
        $this->mount($primaries, $secondaries);
    }
}
