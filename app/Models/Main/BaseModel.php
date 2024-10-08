<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Auth;
class BaseModel extends ModelDBMain
{

    protected $connection = 'main';
	public static function scope(){
		return self::whereIn('account_id', [0, Auth::user()->account_id]);
	}
}
