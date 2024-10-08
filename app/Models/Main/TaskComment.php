<?php namespace App\Models\Main;

use Utils;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Task
 */
class TaskComment extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    /**
     * @var array
     */

    /**
     * @return mixed
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

}
