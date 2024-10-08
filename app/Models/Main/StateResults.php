<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class StateResults extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'state_result';
    protected $fillable = [
        'account_id',
        'user_id',
        'from_date',
        'to_date',
        'public_notes',
        'real_employee_id',
        'perdida_en_facturacion',
        'costo_de_lo_vendido',
        'ingresos_de_taller',
        'utilidad_bruta',
        'ventas_netas',
        'gastos_operativos',
        'renta',
        'planilla_sueldo_base',
        'comisiones',
        'internet',
        'plan_telefonico',
        'energia_electrica',
        'agua',
        'publicidad_local',
        'seguridad_de_tienda',
        'utilidad_operativa',
        'gastos_variables',
        'gastos_de_tienda',
        'envios',
        'agua_purificada',
        'regalias',
        'combustible',
        'material_de_aseo',
        'material_de_empaque',
        'papeleria',
        'mantenimiento_de_moto',
        'inversion_en_tienda',
        'impuestos_municipales',
        'camara_de_comercio',
        'equipo_de_computo',
        'politica_de_cumpleaños',
        'gastos_medicos',
        'bono_liga_km',
        'multa_de_transito',
        'politica_de_alimentacion',
        'viaticos_empleados',
        'tren_de_aseo',
        'gastos_de_mercadeo_activacion',
        'gastos_festivos_decoracion_y_otros',
        'utilidad_antes_del_prorrateo',
        'gasto_de_porrateo',
        'utilidad_neta',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account', 'account_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo('App\Models\Main\User', 'user_id', 'id');
    }

    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
        ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    } 
    
}
