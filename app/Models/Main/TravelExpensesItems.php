<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class TravelExpensesItems extends ModelDBMain
{

    protected $connection = 'main';
  protected $table = 'travel_expenses_items';

      /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee');
    }
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function to_account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'to_account_id' );
    }

}
