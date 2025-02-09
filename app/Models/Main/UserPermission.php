<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends ModelDBMain
{
    protected $connection = 'main';
    protected $table = 'user_permissions';

    public function resource(){
        return $this->belongsTo('App\Models\Main\UserResource', 'resource_id', 'id');
    }
}
