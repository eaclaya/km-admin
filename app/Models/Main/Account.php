<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'main';
    protected $table = 'accounts';
}
