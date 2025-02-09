<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Exception;

class ModelDBMain extends Model
{
    protected $connection = 'main';

    /* protected static function boot(): void
    {
        parent::boot();

        static::updating(function ($model) {
            throw new Exception('No se puede actualizar este modelo directamente');
        });

        static::creating(function ($model) {
            throw new Exception('No se puede crear este modelo directamente');
        });

        static::deleting(function ($model) {
            throw new Exception('No se puede eliminar este modelo directamente');
        });
    } */
}
