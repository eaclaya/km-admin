<?php
namespace App\Repositories;

use App\Models\FinanceDaybookEntry;
use Illuminate\Support\Arr;

class DaybookRepository
{
    public function createNew($entry,$items): bool
    {
        if(!$this->thisIsset($entry)){
            $model = new FinanceDaybookEntry();
            $model->account_id = Arr::get($entry,'account_id');
            $model->organization_company_id = Arr::get($entry,'organization_company_id');
            $model->sort_account = $this->getNextSortAccount(Arr::get($entry,'account_id'));
            $model->sort_company = $this->getNextSortCompany(Arr::get($entry,'organization_company_id'));
            $model->description = Arr::get($entry,'description');
            $model->user_id = Arr::get($entry,'user_id');
            $model->real_user_id = Arr::get($entry,'real_user_id');
            $model->partial = Arr::get($entry,'partial') ? floatval(Arr::get($entry,'partial')) : 0;
            $model->debit = Arr::get($entry,'debit') ? floatval(Arr::get($entry,'debit')) : 0;
            $model->havings = Arr::get($entry,'havings') ? floatval(Arr::get($entry,'havings')) : 0;
            $model->created_at = Arr::get($entry,'created_at');
            $model->updated_at = Arr::get($entry,'updated_at');
            $model->model = Arr::get($entry,'model');
            $model->model_id = Arr::get($entry,'model_id');
            $model->save();
            $model->items()->createMany($items);
            return true;
        }
        return false;
    }
    public function getNextSortAccount($account_id){
        if(!is_null($account_id)){
            $sort_account = FinanceDaybookEntry::where('account_id', $account_id)->max('sort_account');
            if(is_null($sort_account)){
                $sort_account = 1;
            }else{
                $sort_account = $sort_account + 1;
            }
        }
        return $sort_account;
    }
    public function getNextSortCompany($organization_company_id){
        if(!is_null($organization_company_id)){
            $sort_company = FinanceDaybookEntry::where('organization_company_id', $organization_company_id)->max('sort_company');
            if(is_null($sort_company)){
                $sort_company = 1;
            }else{
                $sort_company = $sort_company + 1;
            }
        }
        return $sort_company;
    }
    public function thisIsset($entry):bool
    {
        $subset = Arr::only($entry, [
            'account_id',
            'organization_company_id',
            'description',
            'created_at',
            'model',
            'model_id'
        ]);
        return FinanceDaybookEntry::where($subset)->exists();
    }

}
