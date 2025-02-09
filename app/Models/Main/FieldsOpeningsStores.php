<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FieldsOpeningsStores extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'fields_openings_stores';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'comment',
        'area_id',
    ];
}
