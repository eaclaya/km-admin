<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceDaybookEntryItem extends Model
{
    use SoftDeletes;

    protected $table = "finance_daybook_entry_item";

    /**
     * @var array
     */
    protected $fillable = [
        'finance_daybook_entry_id',
        'account_id',
        'organization_company_id',
        'description',
        'finance_catalogue_item_id',
        'model',
        'model_id',
        'partial',
        'debit',
        'havings'
    ];
}
