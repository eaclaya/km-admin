<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTrackingTransfers extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'task_tracking_tansfers';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'user_id',
      'real_user_id',
      'from_employee_id',
      'from_profile_id',
      'from_task_id',
      'to_employee_id',
      'to_profile_id',
      'to_task_id',
    ];

  public function fromEmployee(){	
	  return $this->belongsTo('App\Models\Main\Employee', 'from_employee_id', 'id');
  }
  public function toEmployee(){	
	  return $this->belongsTo('App\Models\Main\Employee', 'to_employee_id', 'id');
  }
  public function fromProfile(){	
	  return $this->belongsTo('App\Models\Main\EmployeeProfile', 'from_profile_id', 'id');
  }
  public function toProfile(){	
	  return $this->belongsTo('App\Models\Main\EmployeeProfile', 'to_profile_id', 'id');
  }  
  public function fromTask(){	
	  return $this->belongsTo('App\Models\Main\TaskKpis', 'from_task_id', 'id');
  }
  public function toTask(){	
	  return $this->belongsTo('App\Models\Main\TaskKpis', 'to_task_id', 'id');
  }
  public function user()
  {
    return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
  }
  public function realUser()
  {
    return $this->belongsTo('App\Models\Main\User', 'real_user_id', 'id');
  }
}
