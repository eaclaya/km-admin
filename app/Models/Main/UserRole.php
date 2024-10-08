<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class UserAccount
 */
class UserRole extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public function permissions(){
        return $this->hasMany('App\Models\Main\UserPermission', 'role_id', 'id');
    }
    public function getEntityType()
    {
        return ENTITY_ROLE;
    }
}
