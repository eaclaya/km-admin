<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Kpis extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'kpis';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'title',
      'description',
      'comment',
      'finish',
      'start',
      'end',
      'task_kpis_id',
      'employee_id',
    ];

  public function employee(){
    return $this->belongsTo('App\Models\Main\Employee', 'employee_id', 'id');
  }
  public function task(){	
    return $this->belongsTo('App\Models\Main\TaskKpis', 'task_kpis_id', 'id');
  }
}
