<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class ProductImportTracking extends ModelDBMain
{

    protected $connection = 'main';
	protected $primaryKey = 'id';
	protected $table = 'products_import_tracking';
	public $timestamps = false;
}
