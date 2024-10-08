<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TicketsPromotions extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'tickets_promotions';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'public_id',
        'promotions_id',
        'account_id',
        'invoice_id',
        'client_id',
        'dimiss',
        'is_winner',
    ];

    /**
     * @return mixed
     */
    public function promotion()
    {
        return $this->belongsTo('App\Models\Main\Promotions', 'promotions_id', 'id');
    }

    /**
     * @return mixed
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client', 'client_id', 'id');
    }

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice', 'invoice_id', 'id');
    }

    public function hash()
    {
        return route('promotions.view',['promotionsId' => $this->promotions_id, 'publicId' => $this->public_id]);
    }

    public function getNextPublicId()
    {
        $lastTicket = \App\Models\Main\TicketsPromotions::withTrashed()->where('promotions_id', $this->promotions_id)->orderBy('public_id', 'DESC')->first();
        $public_id = isset($lastTicket->public_id) ? $lastTicket->public_id + 1 : 1;
        return $public_id;
    }
}
