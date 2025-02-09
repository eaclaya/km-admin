<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends ModelDBMain
{

    protected $connection = 'main';
    protected $primaryKey = 'warehouse_id';
    public $timestamps = false;
}
