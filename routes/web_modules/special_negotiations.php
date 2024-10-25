<?php

use App\Http\Controllers\SpecialNegotiationsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('special_negotiations', SpecialNegotiationsController::class);
});
