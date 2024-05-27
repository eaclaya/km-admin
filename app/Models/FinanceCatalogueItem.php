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

    /**
     * @return mixed
     */
    public function getModel(): mixed
    {
        return DB::connection('main')->table($this->model)->select('id','name')->where('id', $this->model_id)->first();
    }

    /**
     * @return mixed
     */
    public function subItems(): mixed
    {
        return $this->hasMany('App\Models\FinanceCatalogueItem', 'sub_item_id', 'id');
    }

}
