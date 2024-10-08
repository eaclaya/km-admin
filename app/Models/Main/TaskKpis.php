<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaskKpis extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'task_kpis';

    /**
     * @var array
     */
    protected $fillable = [
      'id',
      'name',
      'description',
      'employee_id',
      'profile_id',
      'type',
      'active',
    ];

  public function employee(){	
    return $this->belongsTo('App\Models\Main\Employee', 'employee_id', 'id');
  }

  public function profile(){	
    return $this->belongsTo('App\Models\Main\EmployeeProfile', 'profile_id', 'id');
  }

  public function kpis(){	
    return $this->hasMany('App\Models\Main\Kpis', 'task_kpis_id', 'id');
  }

  public function typeName(){	
    $kpiType = [
      0 => 'Diaria',
      1 => 'Unica',
      2 => 'Semanal',
      3 => 'Quincenal',
      4 => 'Mensual',
      5 => 'Anual',
      6 => 'Solo los Lunes',
      7 => 'Solo los Martes',
      8 => 'Solo los Miercoles',
      9 => 'Solo los Jueves',
      10 => 'Solo los Viernes',
      11 => 'Solo los Sabado',
      12 => 'Solo los Domingo',
    ];
    return $kpiType[$this->type];
  }
}
