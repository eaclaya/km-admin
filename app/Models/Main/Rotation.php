<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Rotation extends ModelDBMain
{

    protected $connection = 'main';
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'rotations';

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ROTATION;
    }
}
