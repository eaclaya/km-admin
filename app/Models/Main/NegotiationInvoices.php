<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class NegotiationInvoices extends ModelDBMain
{
    use SoftDeletes;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'negotiations_invoices';

    protected $fillable = [
        'invoice_id',
        'special_negotiations_id'
    ];

    public function invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice');
    }

    public function special_negotiations()
    {
        return $this->hasMany('App\Models\Main\SpecialNegotiations');
    }
}
