<?php

namespace App\Models;

use App\Models\Main\Account;
use App\Models\Main\OrganizationCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class FinanceDaybookEntry extends Model
{
    use SoftDeletes;

    protected $table = "finance_daybook_entry";

    /**
     * @var array
     */
    protected $fillable = [
        'sort_account',
        'sort_company',
        'account_id',
        'organization_company_id',
        'description',
        'user_id',
        'real_user_id',
        'partial',
        'debit',
        'havings',
        'model',
        'model_id'
    ];

    public function items()
    {
        return $this->hasMany('App\Models\FinanceDaybookEntryItem', 'finance_daybook_entry_id', 'id');
    }

    public function getAccountNameAttribute(): string
    {
        $account_id = $this->account_id;
        $account = Account::on('main')->where('id',$account_id)->select('name')->first();
        if(!$account){
            return '';
        }
        return $account->name;
    }
    public function getCompanyNameAttribute(): string
    {
        $company_id = $this->organization_company_id;
        $company = OrganizationCompany::on('main')->where('id',$company_id)->select('name')->first();
        if(!$company){
            return '';
        }
        return $company->name;
    }
}
