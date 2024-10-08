<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class DatetimeFormat
 */
class DatetimeFormat extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return bool|string
     */
    public function __toString()
    {
        $date = mktime(0, 0, 0, 12, 31, date('Y'));

        return date($this->format, $date);
    }    
}
