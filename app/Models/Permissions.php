<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permissions extends Model
{
    protected $table = 'user_permissions';
    protected $connection = 'main';
    protected $fillable = [
        'resource_id',
        'resource_code',
        'role_id',
    ];

    public function user() :BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function role() :BelongsTo
    {
        return $this->belongsTo('App\Models\Roles', 'role_id');
    }
}