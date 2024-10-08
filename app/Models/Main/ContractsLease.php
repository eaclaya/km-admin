<?php namespace App\Models\Main;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractsLease extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $table = 'contracts_leases';

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }
}
