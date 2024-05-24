<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCatalogueClassification extends Model
{
    use SoftDeletes;

    protected $table = "finance_catalogue_classification";

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'sort',
        'items_qty',
        'color',
    ];
}
