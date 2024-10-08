<?php namespace App\Models\Main;

use Utils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Task
 */
class Task extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    use PresentableTrait;

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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TASK;
    }

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\TaskPresenter';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Main\TaskStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice');
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

    /**
     * @param $task
     * @return string
     */
    public static function calcStartTime($task)
    {
        $parts = json_decode($task->time_log) ?: [];

        if (count($parts)) {
            return Utils::timestampToDateTimeString($parts[0][0]);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return self::calcStartTime($this);
    }

    public function getLastStartTime()
    {
      $parts = json_decode($this->time_log) ?: [];

      if (count($parts)) {
          $index = count($parts) - 1;
          return $parts[$index][0];
      } else {
          return '';
      }
    }

    /**
     * @param $task
     * @return int
     */
    public static function calcDuration($task)
    {
        $duration = 0;
        $parts = json_decode($task->time_log) ?: [];

        foreach ($parts as $part) {
            if (count($part) == 1 || !$part[1]) {
                $duration += time() - $part[0];
            } else {
                $duration += $part[1] - $part[0];
            }
        }

        return $duration;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return self::calcDuration($this);
    }

    /**
     * @return int
     */
    public function getCurrentDuration()
    {
        $parts = json_decode($this->time_log) ?: [];
        $part = $parts[count($parts)-1];

        if (count($part) == 1 || !$part[1]) {
            return time() - $part[0];
        } else {
            return 0;
        }
    }

    /**
     * @return bool
     */
    public function hasPreviousDuration()
    {
        $parts = json_decode($this->time_log) ?: [];
        return count($parts) && (count($parts[0]) && $parts[0][1]);
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return round($this->getDuration() / (60 * 60), 2);
    }

    /**
     * Gets the route to the tasks edit action
     *
     * @return string
     */
    public function getRoute()
    {
        return "/tasks/{$this->id}/edit";
    }

    public function getName()
    {
        return '#' . $this->public_id;
    }

    public function getDisplayName()
    {
        if ($this->description) {
            return mb_strimwidth($this->description, 0, 16, "...");
        }

        return '#' . $this->public_id;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        $query->whereRaw('cast(substring(time_log, 3, 10) as unsigned) >= ' . $startDate->format('U'));
        $query->whereRaw('cast(substring(time_log, 3, 10) as unsigned) <= ' . $endDate->format('U'));

        return $query;
    }
}


