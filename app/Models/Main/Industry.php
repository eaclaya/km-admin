<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class Industry
 */
class Industry extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function getName() 
    {
        return $this->name;
    }
}
