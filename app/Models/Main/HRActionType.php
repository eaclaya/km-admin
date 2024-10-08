<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class HRActionType extends ModelDBMain
{

    protected $connection = 'main';

    protected $guarded = [];
    protected $table = 'h_r_action_types';

    public function category(){
        return $this->belongsTo(HRActionCategory::class, 'category_id', 'id');
    }
}
