<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Brand extends ModelDBMain
{

    protected $connection = 'main';

    protected $primaryKey = 'brand_id';
    
    public $timestamps = false;
    

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_BRAND;
    }

    
    public function getFirstOrNew($name = null){
        if(!is_null($name)){
            $brand = Brand::where('name', $name)->first();
            if(!$brand){
                $this->name = $name;
                $this->save();
                return $this;
            }
            return $brand;
        }
    }
}
