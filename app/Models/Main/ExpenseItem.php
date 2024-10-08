<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
/**
 * Class ExpenseCategory
 */
class ExpenseItem extends ModelDBMain
{

    protected $connection = 'main';
    public function expense()
    {
        return $this->belongsTo('App\Models\Main\Expense');
    }
	

}
