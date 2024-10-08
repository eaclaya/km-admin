<?php namespace App\Models\Main;

use Utils;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Board
 */
class Board extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'description',
        'time_log',
        'is_running',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_BOARD;
    }

    

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Main\Task');
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
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee')->withTrashed();
    }

    public function employees(){
	return $this->hasMany('App\Models\Main\BoardEmployee');
    }

    /**
     * Gets the route to the tasks edit action
     *
     * @return string
     */
    public function getRoute()
    {
        return "/boards/{$this->id}/edit";
    }

    public function getName()
    {
        return '#' . $this->public_id;
    }


}


