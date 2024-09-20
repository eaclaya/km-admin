<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceBankAccount extends ModelDBMain
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'finance_banks_accounts';

    /**
     * @var array
     */
    protected $fillable = [
        'organization_company_id',
        'bank_id',
        'currency_id',
        'account_number',
        'username',
    ];

    /**
     * @return mixed
     */
    public function organizationCompany()
    {
        return $this->belongsTo('App\Models\Main\OrganizationCompany', 'organization_company_id', 'id');
    }

    public function bank(){
        return $this->belongsTo('App\Models\Main\FinanceBank', 'bank_id', 'id');
    }

    public function currency(){
        return $this->belongsTo('App\Models\Main\Currency', 'currency_id', 'id');
    }
}
