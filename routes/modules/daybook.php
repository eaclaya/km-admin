<?php

use App\Livewire\DayBook;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('/daybook', DayBook::class)->middleware('checkPermission:whatsapp_config');
});
