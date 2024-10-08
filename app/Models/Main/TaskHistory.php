<?php namespace App\Models\Main;

use Utils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Task
 */
class TaskHistory extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    use PresentableTrait;
    protected $table = 'tasks_history';
    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'description',
        'time_log',
        'is_running',
        'status_id',
    ];

    public function status()
    {
        return $this->belongsTo('App\Models\Main\TaskStatus');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

}
