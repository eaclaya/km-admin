<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class UserAccount
 */
class UserAccount extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $userId
     * @return bool
     */
    public function hasUserId($userId)
    {
        if (!$userId) {
            return false;
        }
        //PERMITIR MAS DE 5 TIENDAS O USUARIOS
        for ($i=1; $i<=100; $i++) {
            $field = "user_id{$i}";
            if ($this->$field && $this->$field == $userId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId)
    {
        if (self::hasUserId($userId)) {
            return;
        }

        for ($i=1; $i<=5; $i++) {
            $field = "user_id{$i}";
            if (!$this->$field) {
                $this->$field = $userId;
                break;
            }
        }
    }

    /**
     * @param $userId
     */
    public function removeUserId($userId)
    {
        if (!$userId || !self::hasUserId($userId)) {
            return;
        }

        for ($i=1; $i<=5; $i++) {
            $field = "user_id{$i}";
            if ($this->$field && $this->$field == $userId) {
                $this->$field = null;
            }
        }
    }
}
