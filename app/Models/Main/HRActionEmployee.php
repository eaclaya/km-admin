<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class HRActionEmployee extends ModelDBMain
{

    protected $connection = 'main';

    protected $fillable = [
        'from_date',
        'to_date',
        'employee_id',
        'account_id',
        'comments',
    ];
    protected $table = 'h_r_action_employees';

    public function account(){
	return $this->belongsTo(Account::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function items(){
        return $this->hasMany(HRActionEmployeeSelectedItem::class, 'action_id', 'id');
    }
}
