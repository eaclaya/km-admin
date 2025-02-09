<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Billing extends ModelDBMain
{

    protected $connection = 'main';
    //
    protected $table = 'billing';
    protected $primaryKey = 'billing_id';
    public $timestamps = false;
}
