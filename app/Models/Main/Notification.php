<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Notification extends ModelDBMain
{

    protected $connection = 'main';
    //
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;
}
