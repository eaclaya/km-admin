<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class VacationDaysTakenEmployees extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'vacation_days_taken_employees';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'real_user_id',
        'employee_id',
        'titulo',
        'description',
        'start_date',
        'end_date',
        'active',
    ];

    /**
     * @return mixed
     */
    public function realUser()
    {
        return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
    }
    
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Main\Employee', 'employee_id', 'id');
    }
}
