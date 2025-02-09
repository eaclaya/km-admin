<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class ExpenseCategory
 */
class IncomeCategory extends ModelDBMain
{

    protected $connection = 'main';
    // Expense Categories
    use SoftDeletes;


    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\EntityPresenter';

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_INCOME_CATEGORY;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function income()
    {
        return $this->belongsTo('App\Models\Main\MoneyIncome');
    }

    public function getRoute()
    {
        return "/income_categories/{$this->public_id}/edit";
    }

}
