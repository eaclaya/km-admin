<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Promotions extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = "promotions";

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'is_active',
        'name',
        'descriptcion',
        'message',
        'dimiss_message',
        'file',
        'ticket_format',
        'normal_total_billed',
        'wholesaler_total_billed',
        'start_date',
        'end_date',
        'draw_date'
    ];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function tickets()
    {
        return $this->hasMany('App\Models\Main\TicketsPromotions', 'promotions_id', 'id');
    }
    
}
