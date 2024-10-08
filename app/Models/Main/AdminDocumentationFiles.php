<?php namespace App\Models\Main;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 */
class AdminDocumentationFiles extends ModelDBMain
{

    protected $connection = 'main';    use SoftDeletes;

    protected $table = 'admin_documentation_files';

    protected $fillable = [
        'admin_documentation_id',
        'file_type_id',
        'file'
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function documentation()
    {
        return $this->belongsTo('App\Models\Main\AdminDocumentation', 'admin_documentation_id');
    }

}
