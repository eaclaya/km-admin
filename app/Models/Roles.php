<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Roles extends Model
{
    protected $table = 'user_roles';
    protected $connection = 'main';
    public function permissions(): HasMany
    {
        return $this->hasMany('App\Models\Permissions', 'role_id');
    }
}