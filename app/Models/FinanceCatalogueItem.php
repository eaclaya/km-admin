<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FinanceCatalogueItem extends Model
{
    use SoftDeletes;

    protected $table = "finance_catalogue_item";

    /**
     * @var array
     */
    protected $fillable = [
        'finance_account_name',
        'sub_item_id',
        'sort',
        'finance_catalogue_classification_sort',
        'model',
        'model_id',
    ];

    public function getModel(): mixed
    {
        return DB::connection('main')->table($this->model)->select('id','name')->where('id', $this->model_id)->first();
    }

    public function subItems(): mixed
    {
        return $this->hasMany('App\Models\FinanceCatalogueItem', 'sub_item_id', 'id');
    }

    public function getClassificationNumberAttribute(): mixed
    {
        $clasifications = FinanceCatalogueClassification::orderByDesc('sort')->pluck('items_qty','sort')->toArray();
        $number = $this->getNumbers($clasifications,$this);
        return implode('-', array_reverse($number));
    }

    public function getNumbers($clasifications,$supraItem): array
    {
        $classification = isset($clasifications[$supraItem->finance_catalogue_classification_sort]) ? $clasifications[$supraItem->finance_catalogue_classification_sort] : 1;
        $number = [str_pad($supraItem->sort, $classification, "0", STR_PAD_LEFT)];

        if(isset($supraItem->sub_item_id) && $supraItem->sub_item_id > 0){
            $upSupraItem = FinanceCatalogueItem::where('id', $supraItem->sub_item_id)->select(['sub_item_id','sort','finance_catalogue_classification_sort'])->first();
            $number = array_merge($number, $this->getNumbers($clasifications,$upSupraItem));
        }

        return $number;
    }
}
