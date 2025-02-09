<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class HRActionCategory extends ModelDBMain
{

    protected $connection = 'main';
    protected $guarded = [];
    protected $table = 'h_r_action_categories';
    
    public function types(){
        return $this->hasMany(HRActionType::class, 'category_id', 'id');
    }
}
