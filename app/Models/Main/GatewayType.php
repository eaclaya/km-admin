<?php namespace App\Models\Main;

use Eloquent;
use Cache;
use Utils;

/**
 * Class GatewayType
 */
class GatewayType extends ModelDBMain
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

    public static function getAliasFromId($id)
    {
        return Utils::getFromCache($id, 'gatewayTypes')->alias;
    }

    public static function getIdFromAlias($alias)
    {
        return Cache::get('gatewayTypes')->where('alias', $alias)->first()->id;
    }
}
