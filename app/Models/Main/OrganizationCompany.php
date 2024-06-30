<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class OrganizationCompany extends ModelDBMain
{
    protected $table = 'organization_company';

    protected $fillable = [
        'id',
        'name',
    ];

    public function accounts()
    {
        return $this->hasMany('App\Models\Main\Account', 'organization_company_id', 'id');
    }
}
