<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcredorsItems extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

}
