<?php namespace App\Models\Main;

use Auth;

/**
 * Class AccountGatewaySettings
 */
class AccountGatewaySettings extends ModelDBMain
{

    protected $connection = 'main';
    /**
     * @var array
     */
    protected $dates = ['updated_at'];

    /**
     * @var bool
     */
    protected static $hasPublicId = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gatewayType()
    {
        return $this->belongsTo('App\Models\Main\GatewayType');
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }
}
