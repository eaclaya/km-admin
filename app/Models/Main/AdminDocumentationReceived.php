<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class AdminDocumentationReceived extends ModelDBMain
{

    protected $connection = 'main';    use SoftDeletes;

    protected $table = 'admin_documentation_received';

    protected $fillable = [
        'admin_documentation_id',
        'account_id',
        'received_user_id',
        'received_real_user_id',
        'received_at',
        'confirmed_user_id',
        'confirmed_real_user_id',
        'confirmed_at'
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function documentation()
    {
        return $this->belongsTo('App\Models\Main\AdminDocumentation', 'admin_documentation_id');
    }
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id');
    }
    public function receivedUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'received_user_id');
    }
    public function receivedRealUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'received_real_user_id');
    }
    public function confirmedUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'confirmed_user_id');
    }
    public function confirmedRealUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'confirmed_real_user_id');
    }
}
