<?php

use App\Livewire\DayBook;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/daybook', DayBook::class);
});
