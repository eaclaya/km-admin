<?php
namespace App\Repositories;

use App\Models\FinanceCatalogueItem;
use Illuminate\Support\Arr;

class FinanceCatalogueRepository
{
    public function getItemForNumber($item):array
    {
        $items = FinanceCatalogueItem::orderBy('sort', 'ASC')->whereNull('sub_item_id')->with('subItems');
        Arr::get($item, 'finance_catalogue_classification_sort');
        return [];
    }
}
