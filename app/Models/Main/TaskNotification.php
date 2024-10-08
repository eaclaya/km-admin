<?php namespace App\Models\Main;

use Utils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Task
 */
class TaskNotification extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    use PresentableTrait;
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

}
