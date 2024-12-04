<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::to('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

//test datatable products charged
Route::get('products', function () {
    return view('products.index');
})->middleware(['auth'])->name('products.index');


require __DIR__ . '/web_modules/daybook.php';
require __DIR__ . '/web_modules/invoice_discount.php';
require __DIR__ . '/web_modules/clone_models.php';
require __DIR__ . '/web_modules/setup_menu.php';
require __DIR__ . '/web_modules/permissions.php';
/* require __DIR__ . '/web_modules/advance_reports.php'; */
require __DIR__ . '/web_modules/special_negotiations.php';

require __DIR__.'/auth.php';
