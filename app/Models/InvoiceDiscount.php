<?php

namespace App\Models;

use App\Models\Main\Account;
use Illuminate\Database\Eloquent\Model;

class InvoiceDiscount extends Model
{
    protected $table = "invoices_discount";
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->hasMany('App\Models\InvoiceItemDiscount', 'invoice_id', 'invoice_id');
    }

    public function getAccountNameAttribute(): string
    {
        $account_id = $this->account_id;
        $account = Account::on('main')->where('id',$account_id)->select('name')->first();
        if(!$account){
            return '';
        }
        return $account->name;
    }
}
