<?php namespace App\Models\Main;

use Auth;
use Eloquent;
use Illuminate\Database\QueryException;
use Utils;
use Validator;
use URL;
use ROUTE;

/**
 * Class EntityModel
 */
class EntityModel extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var bool
     */
    protected static $hasPublicId = true;

    /**
     * @var array
     */
   // protected $hidden = ['id'];

    /**
     * @var bool
     */
    public static $notifySubscriptions = true;

    /**
     * @param null $context
     * @return mixed
     */
    public static function createNew($context = null)
    {
        $className = get_called_class();
        $entity = new $className();

        if ($context) {
            $user = $context instanceof User ? $context : $context->user;
            $account = $context->account;
        } elseif (Auth::check()) {
            $user = Auth::user();
            $account = Auth::user()->account;
        } else {
            Utils::fatalError();
        }

        $entity->user_id = $user->id;
        $entity->account_id = $account->id;

        // store references to the original user/account to prevent needing to reload them
        $entity->setRelation('user', $user);
        $entity->setRelation('account', $account);

        if (method_exists($className, 'trashed')){
            $lastEntity = $className::whereAccountId($entity->account_id)->withTrashed();
        } else {
            $lastEntity = $className::whereAccountId($entity->account_id);
        }


        if (static::$hasPublicId) {
            $lastEntity = $lastEntity->orderBy('public_id', 'DESC')
                                     ->first();

            if ($lastEntity) {
                $entity->public_id = $lastEntity->public_id + 1;
            } else {
                $entity->public_id = 1;
            }
        }

        return $entity;
    }

    /**
     * @param $publicId
     * @return mixed
     */
    public static function getPrivateId($publicId)
    {
        $className = get_called_class();

        return $className::scope($publicId)->withTrashed()->value('id');
    }

    /**
     * @return string
     */
    public function getActivityKey()
    {
        return '[' . $this->getEntityType().':'.$this->public_id.':'.$this->getDisplayName() . ']';
    }

    public function entityKey()
    {
        return $this->public_id . ':' . $this->getEntityType();
    }

    public function subEntityType()
    {
        return $this->getEntityType();
    }

    public function isEntityType($type)
    {
        return $this->getEntityType() === $type;
    }

    /*
    public function getEntityType()
    {
        return '';
    }

    public function getNmae()
    {
        return '';
    }
    */

    /**
     * @param $query
     * @param bool $publicId
     * @param bool $accountId
     * @return mixed
     */
    // NEW CODE
    public function scopeScope($query, $publicId = false, $accountId = false, $model = false)
    {
        if (!$accountId) {
            $accountId = Auth::user()->account_id;
        }

        $query->where($this->getTable() .'.account_id', '=', $accountId);
       
        // NEW CODE
        if($model == 'brand')
        {
            $query->where('brand_id', $publicId);
        }
        elseif ($publicId) {
            if (is_array($publicId)) {
                $query->whereIn('public_id', $publicId);
            } else {
                $query->wherePublicId($publicId);
            }
        }
        
        // dd($publicId);

        if (Auth::check() && ! Auth::user()->hasPermission('view_all') && $this->getEntityType() != ENTITY_TAX_RATE) {
            $query->where(Utils::pluralizeEntityType($this->getEntityType()) . '.user_id', '=', Auth::user()->id);
        }
        
        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithArchived($query)
    {
        return $query->withTrashed()->where('is_deleted', '=', false);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->public_id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @param $entityType
     * @return string
     */
    public static function getClassName($entityType)
    {
        return 'App\\Models\\' . ucwords(Utils::toCamelCase($entityType));
    }

    /**
     * @param $entityType
     * @return string
     */
    public static function getTransformerName($entityType)
    {
        return 'App\\Ninja\\Transformers\\' . ucwords(Utils::toCamelCase($entityType)) . 'Transformer';
    }

    public function setNullValues()
    {
        foreach ($this->fillable as $field) {
            if (strstr($field, '_id') && !$this->$field) {
                $this->$field = null;
            }
        }
    }

    // converts "App\Models\Main\Client" to "client_id"
    /**
     * @return string
     */
    public function getKeyField()
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $name = $parts[count($parts)-1];
        return strtolower($name) . '_id';
    }

    /**
     * @param $data
     * @param $entityType
     * @return bool|string
     */
    public static function validate($data, $entityType, $entity = false)
    {
        // Use the API request if it exists
        $action = $entity ? 'update' : 'create';
        $requestClass = sprintf('App\\Http\\Requests\\%s%sAPIRequest', ucwords($action), ucwords($entityType));
        if ( ! class_exists($requestClass)) {
            $requestClass = sprintf('App\\Http\\Requests\\%s%sRequest', ucwords($action), ucwords($entityType));
        }

        $request = new $requestClass();
        $request->setUserResolver(
            function() { 
                if(Auth::check()){
                    return Auth::user();
                }else{
                    return \App\Models\Main\User::find(16);
                }
            }
        );
        $request->setEntity($entity);
        $request->replace($data);

        if ( ! $request->authorize()) {
            return trans('texts.not_allowed');
        }

        $validator = Validator::make($data, $request->rules());

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return true;
        }
    }

    public static function getIcon($entityType)
    {
        $icons = [
            'dashboard' => 'tachometer',
            'clients' => 'users',
            'products' => 'cube',
            'invoices' => 'file-pdf-o',
            'payments' => 'credit-card',
            'recurring_invoices' => 'files-o',
            'credits' => 'credit-card',
            'quotes' => 'file-text-o',
            'tasks' => 'clock-o',
            'expenses' => 'file-image-o',
            'vendors' => 'building',
            'settings' => 'cog',
            'self-update' => 'download',
        ];

        return array_get($icons, $entityType);
    }

    // isDirty return true if the field's new value is the same as the old one
    public function isChanged()
    {
        foreach ($this->fillable as $field) {
            if ($this->$field != $this->getOriginal($field)) {
                return true;
            }
        }

        return false;
    }
}
