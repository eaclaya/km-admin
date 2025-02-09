<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class AuthenticationToken extends ModelDBMain
{

    protected $connection = 'main';
	protected $table = "authentication_tokens";

}
