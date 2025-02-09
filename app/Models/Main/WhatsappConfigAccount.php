<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappConfigAccount extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'whatsapp_config_account';

    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'instance_id',
        'access_token',
        'invoices_created_message',
        'invoices_updated_message',
        'client_created_message',
        'client_updated_message',
        'personalized_message',
        'active_messages',
        'active_personalized_message',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function isInvalidate(){
        if(!$this->active_messages){
            return true;
        }
        if(!$this->instance_id || !$this->access_token){
            return true;
        }
        return false;
    }

}
