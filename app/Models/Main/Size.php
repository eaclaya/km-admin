<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class Size
 */
class Size extends ModelDBMain
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