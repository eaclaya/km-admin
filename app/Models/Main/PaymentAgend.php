<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentAgend extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    protected $table = 'payment_agends';

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }
    
    public function payment_agends_items(){	
        return $this->hasMany('App\Models\Main\PaymentAgendItem', 'payment_agend_id')->orderBy('date');
    }
}
