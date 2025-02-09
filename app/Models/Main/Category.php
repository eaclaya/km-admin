<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Category extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'category_id';
    
    public $timestamps = false;

    public $incrementing = false;
    

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CATEGORY;
    }
}
