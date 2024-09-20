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
        'class_number',
        'is_generated'
    ];

    public function getModel(): mixed
    {
        if($this->model == 'finance_banks_accounts'){
            return DB::connection('main')->table($this->model)->select('id','account_number as name')->where('id', $this->model_id)->first();
        }
        return DB::connection('main')->table($this->model)->select('id','name')->where('id', $this->model_id)->first();
    }

    public function subItems(): mixed
    {
        return $this->hasMany('App\Models\FinanceCatalogueItem', 'sub_item_id', 'id')->orderBy('sort');
    }

    public function subItemsWithModel(): mixed
    {
        return $this->hasMany('App\Models\FinanceCatalogueItem', 'sub_item_id', 'id')
            ->whereNotNull('model');
    }

    public function subItemsWithNotModel(): mixed
    {
        return $this->hasMany('App\Models\FinanceCatalogueItem', 'sub_item_id', 'id')
            ->whereNull('model');
    }

    public function setNumberAttribute(): mixed
    {
        $classifications = FinanceCatalogueClassification::orderByDesc('sort')->pluck('items_qty','sort')->toArray();
        $number = $this->getNumbers($classifications,$this);
        $this->class_number = implode('-', array_reverse($number));
        $this->save();
        return $this->class_number;
    }

    public function getNumbers($classifications,$supraItem): array
    {
        $classification = isset($classifications[$supraItem->finance_catalogue_classification_sort]) ? $classifications[$supraItem->finance_catalogue_classification_sort] : 1;
        $number = [str_pad($supraItem->sort, $classification, "0", STR_PAD_LEFT)];

        if(isset($supraItem->sub_item_id) && $supraItem->sub_item_id > 0){
            $upSupraItem = FinanceCatalogueItem::where('id', $supraItem->sub_item_id)->select(['sub_item_id','sort','finance_catalogue_classification_sort'])->first();
            $number = array_merge($number, $this->getNumbers($classifications,$upSupraItem));
        }
        return $number;
    }

    public function getNumberAttribute(){
        if (isset($this->class_number) && trim($this->class_number) !== '') {
            return $this->class_number;
        }else{
            return $this->setNumberAttribute();
        }
    }
}
