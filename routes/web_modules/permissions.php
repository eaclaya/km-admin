<?php

use App\Http\Controllers\PermissionsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    /*Route::get('permissions/complete/{clone_id}',[PermissionsController::class, 'complete'])
        ->name('permissions.complete');*/

    Route::resource('permissions', PermissionsController::class);
});
