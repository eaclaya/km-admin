<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tutorial extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    protected $table = 'tutorial';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'link',
        'tutorial_category_id',
        'active',
    ];

    /**
     * @return mixed
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Main\TutorialCategory', 'tutorial_category_id', 'id');
    }
}
