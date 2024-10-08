<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class UserAccount
 */
class UserPermission extends ModelDBMain
{

    protected $connection = 'main';    public function resource(){
        return $this->belongsTo('App\Models\Main\UserResource', 'resource_id', 'id');
    }
}
