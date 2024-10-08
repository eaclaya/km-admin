<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskStatus extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $table = 'task_status';
}
