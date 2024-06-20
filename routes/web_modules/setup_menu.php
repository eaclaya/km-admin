<?php

use App\Http\Controllers\SetupMenuController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','checkPermission'])->group(function () {
    Route::get('setup_menu', [SetupMenuController::class, 'index'])->name('setup_menu.index');

    Route::post('setup_menu/create', [SetupMenuController::class, 'create'])->name('setup_menu.create');

    Route::post('setup_menu', [SetupMenuController::class, 'store'])->name('setup_menu.store');

    Route::get('setup_menu/{id}', [SetupMenuController::class, 'destroy'])->name('setup_menu.destroy');
});
