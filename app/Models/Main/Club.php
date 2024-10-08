<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Club extends ModelDBMain
{

    protected $connection = 'main';

    

    public function members()
    {
        return $this->hasMany('App\Models\Main\ClubMember', 'club_id', 'id');
    }

    public function client_points(){
	return $this->hasMany('App\Models\Main\ClientPoint', 'club_id', 'id');
    }

}
