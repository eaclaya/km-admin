<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Gallery extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'gallery';
}
