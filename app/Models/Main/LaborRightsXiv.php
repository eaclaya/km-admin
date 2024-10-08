<?php

namespace App\Models\Main;


use Illuminate\Database\Eloquent\Model;

class LaborRightsXiv extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'labor_rights_xiv';

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    public function employee()
    {
	return $this->belongsTo('App\Models\Main\Employee');
    }

    public function labor_rights_xiv_items()
    {
        return $this->hasMany('App\Models\Main\LaborRightsXivItem', 'labor_rights_xiv_id', 'id')->orderBy('id');
    }
}
