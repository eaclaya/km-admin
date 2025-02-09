<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::post('accounts', [AccountController::class, 'getAccountsForEmail'])
    ->name('get.accounts.email');
