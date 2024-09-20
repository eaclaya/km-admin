<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceBank extends ModelDBMain
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'finance_banks';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];
    public function accounts()
    {
        return $this->hasMany('App\Models\Main\FinanceBankAccount', 'bank_id', 'id');
    }
}
