<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SetupMenu extends Model
{
    use SoftDeletes;

    protected $table = "setup_menu";

    /**
     * @var array
     */
    protected $fillable = [
        'supra_menu_id',
        'label',
        'label_color',
        'url',
        'text',
        'icon'
    ];

    /**
     * @return mixed
     */
    public function subItems(): mixed
    {
        return $this->hasMany('App\Models\SetupMenu', 'supra_menu_id', 'id');
    }

}
