<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

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
        'havings'
    ];

    public function createNew($input = null): FinanceDaybookEntry
    {
        $user = isset($input['user']) ? $input['user'] : ((Auth::check()) ? Auth::user() : null);
        $model = new FinanceDaybookEntry();
        $model->account_id = !is_null($user) ? $user->account_id : null;
        $model->organization_company_id = !is_null($user) ? $user->account->organization_company_id : null;
        $model->sort_account = $this->getNextSortAccount($user);
        $model->sort_company = $this->getNextSortCompany($user);
        $model->description = isset($input['description']) ? trim($input['description']) : '';
        $model->user_id = $user->id;
        $model->real_user_id = $user->realUser()->id;
        $model->partial = isset($input['partial']) ? floatval($input['partial']) : 0;
        $model->debit = isset($input['debit']) ? floatval($input['debit']) : 0;
        $model->havings = isset($input['havings']) ? floatval($input['havings']) : 0;
        return $model;
    }
    public function getNextSortAccount($user){
        $sort_account = null;
        if(!is_null($user)){
            $sort_account = FinanceDaybookEntry::orderBy('sort_account', 'desc')->where('account_id', $user->account_id)->first();
        }
        return $sort_account;
    }
    public function getNextSortCompany($user){
        $sort_company = null;
        if(!is_null($user)){
            $sort_company = FinanceDaybookEntry::orderBy('sort_company', 'desc')->where('organization_company_id', $user->account->organization_company_id)->first();
        }
        return $sort_company;
    }
}
