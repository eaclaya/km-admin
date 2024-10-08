<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TutorialCategory extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'tutorial_category';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    /**
     * @return mixed
     */
    public function tutorials()
    {
        return $this->hasMany('App\Models\Main\Tutorial', 'tutorial_category_id', 'id');
    }
}
