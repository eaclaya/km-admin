<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ClubMember extends ModelDBMain
{

    protected $connection = 'main';

    public function club(){
	return $this->belongsTo('App\Models\Main\Club', 'id', 'club_id');
    }

    public function client()
    {
        return $this->belongs('App\Models\Main\Client', 'id', 'client_id');
    }

}
