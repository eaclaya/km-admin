<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = false;
    public $incrementing = false;

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_SUB_CATEGORY;
    }
}
