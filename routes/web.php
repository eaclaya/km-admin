<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::to('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/web_modules/daybook.php';
require __DIR__ . '/web_modules/invoice_discount.php';
require __DIR__ . '/web_modules/clone_models.php';
require __DIR__ . '/web_modules/setup_menu.php';
require __DIR__ . '/web_modules/permissions.php';
require __DIR__ . '/web_modules/advance_reports.php';

require __DIR__.'/auth.php';
