<?php

namespace App\Models\Main;

//use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'main';
    protected $table = 'personal_access_tokens';
}
