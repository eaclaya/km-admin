<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class CashCountTotals extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'cash_count_totals';

    public function account() {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');

    }
}
