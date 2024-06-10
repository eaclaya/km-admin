<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::to('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/modules/daybook.php';
require __DIR__ . '/modules/invoice_discount.php';
require __DIR__ . '/modules/clone_models.php';
require __DIR__ . '/modules/setup_menu.php';

require __DIR__.'/auth.php';
