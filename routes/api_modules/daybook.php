<?php

use App\Http\Controllers\DaybookApiController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

//Nota: El middleware checkPermission se encarga de verificar si el usuario tiene permisos para acceder a la ruta
//Si bien se puede pasar el nombre del permiso directamente (como se ve a continuacion), al no pasarlo se buscara en la base de datos
//el permiso con el nombre de la ruta y se comparara con los permisos del usuario
/*['auth:sanctum']*/

Route::get('get-token', function () {
    return User::find(166)->createToken('auth_token')->plainTextToken;
//    return User::find(166)->tokens;
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('daybook/hook', [DaybookApiController::class, 'hook'])
        ->name('daybook.hook');
});
