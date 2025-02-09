<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ShopifyProduct extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'products';
}
