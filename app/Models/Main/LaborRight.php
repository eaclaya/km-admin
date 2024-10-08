<?php

namespace App\Models\Main;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborRight extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'labor_rights';

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

    public function labor_rights_items()
    {
        return $this->hasMany('App\Models\Main\LaborRightsItem', 'labor_rights_id', 'id')->orderBy('id');
    }
}
