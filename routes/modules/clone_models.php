<?php

use App\Http\Controllers\CloneModelsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('clone_models/{model}/{model_id?}',[CloneModelsController::class, 'list'])
        ->name('clone_models.list');

    Route::resource('clone_models', CloneModelsController::class);
});
