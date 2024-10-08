<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class HRActionEmployeeSelectedItem extends ModelDBMain
{

    protected $connection = 'main';
    protected $fillable = [
        'employee_id',
        'account_id',
        'description',
        'action_type_id',
        'action_id',
    ];
    protected $table = 'h_r_action_employee_selected_items';

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    public function type(){
        return $this->belongsTo(HRActionType::class, 'action_type_id', 'id');
    }
}
