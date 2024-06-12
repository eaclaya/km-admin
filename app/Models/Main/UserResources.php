<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class UserResources extends ModelDBMain
{
    protected $table = 'user_resources';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
