<?php
namespace App\Services;


use App\Models\FinanceCatalogueItem;
use App\Models\FinanceCatalogueClassification;
use App\Models\Main\Account;
use App\Models\Main\OrganizationCompany;
use App\Repositories\FinanceCatalogueRepository;

class FinanceCatalogueService
{
    protected array $typeGenerate = [
        0 => 'Not generate',
        1 => 'Companies and Accounts',
    ];
    Protected array $models = [
        'accounts' => 'Tienda',
        'organization_company' => 'Empresas',
        'expense_category' => 'Categorias de Gastos',
        'expense_subcategory' => 'Sub Categorias de Gastos',
    ];
    protected FinanceCatalogueRepository $catalogueRepository;
    public function __construct(FinanceCatalogueRepository $catalogueRepository)
    {
        $this->catalogueRepository = $catalogueRepository;
    }

    public function getRepo()
    {
        return $this->catalogueRepository;
    }

    public function initGenerate($item):bool
    {
        if ($item->is_generated == 1) {
            $this->processCompaniesAndAccount($item);
        }
        return true;
    }

    public function processCompaniesAndAccount($item){
        $companies = OrganizationCompany::with('accounts')->get();
        $subItemId = $item->id;
        $subItemClassificationSort = $item->finance_catalogue_classification_sort;
        $subItems = $item->subItemsWithModel->keyBy('model_id');
        $itemsNotModel = $item->subItemsWithNotModel;
        $name = $item->finance_account_name;
        $sort = 1;

        foreach ($itemsNotModel as $notModel){
            $notModel->sort = $sort;
            $notModel->finance_catalogue_classification_sort = $subItemClassificationSort + 1;
            $notModel->save();
            $sort = $sort + 1;
        }

        foreach ($companies as $company){
            $itemFilter = isset($subItems[$company->id]) ? $subItems[$company->id] : null;
            if(!isset($itemFilter)){
                $itemFilter = new FinanceCatalogueItem();
            }
            $itemFilter->finance_account_name = $name . ' ' . $company->name;
            $itemFilter->sub_item_id = $subItemId;
            $itemFilter->sort = $sort;
            $itemFilter->finance_catalogue_classification_sort = $subItemClassificationSort + 1;
            $itemFilter->model = 'organization_company';
            $itemFilter->model_id = $company->id;
            $itemFilter->save();
            $itemFilter->setNumberAttribute();
            $sort = $sort + 1;

            $accounts = $company->accounts;
            $subCurrentItemId = $itemFilter->id;
            $subCurrentItems = $itemFilter->subItemsWithModel->keyBy('model_id');
            $subCurrentSort = 1;

            foreach ($accounts as $account){
                $subItemFilter = isset($subCurrentItems[$account->id]) ? $subCurrentItems[$account->id] : null;
                if(!isset($subItemFilter)){
                    $subItemFilter = new FinanceCatalogueItem();
                }else{
                    unset($subCurrentItems[$account->id]);
                }
                $subItemFilter->finance_account_name = $name . ' ' . $account->name;
                $subItemFilter->sub_item_id = $subCurrentItemId;
                $subItemFilter->sort = $subCurrentSort;
                $subItemFilter->finance_catalogue_classification_sort = $subItemClassificationSort + 2;
                $subItemFilter->model = 'accounts';
                $subItemFilter->model_id = $account->id;
                $subItemFilter->save();
                $subItemFilter->setNumberAttribute();
                $subCurrentSort = $subCurrentSort + 1;
            }
            foreach ($subCurrentItems as $subCurrentItem){
                $subCurrentItem->delete();
            }
        }
    }
}
