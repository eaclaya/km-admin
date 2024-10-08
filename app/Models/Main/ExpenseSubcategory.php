<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class ExpenseCategory
 */
class ExpenseSubcategory extends ModelDBMain
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
        return ENTITY_EXPENSE_CATEGORY;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense()
    {
        return $this->belongsTo('App\Models\Main\Expense');
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/expense_categories/{$this->public_id}/edit";
    }

}
