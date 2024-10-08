<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class AdminDocumentation extends ModelDBMain
{

    protected $connection = 'main';    use SoftDeletes;

    protected $table = 'admin_documentation';

    protected $fillable = [
        'name',
        'description',
        'document_type_id',
        'user_id',
        'real_user_id'
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function files()
    {
        return $this->hasMany('App\Models\Main\AdminDocumentationFiles', 'admin_documentation_id');
    }

    public function received()
    {
        return $this->hasMany('App\Models\Main\AdminDocumentationReceived', 'admin_documentation_id');
    }
}
