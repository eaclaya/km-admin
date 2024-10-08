<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class UserRole extends ModelDBMain
{
    protected $table = 'user_roles';

    public function permissions(){
        return $this->hasMany('App\Models\Main\UserPermission', 'role_id', 'id');
    }
    public function getEntityType()
    {
        return ENTITY_ROLE;
    }
}
