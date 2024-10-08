<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FieldsCheckList extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'fields_check_list';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'area_id',
    ];
}
