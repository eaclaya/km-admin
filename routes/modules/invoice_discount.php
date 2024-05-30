<?php

use App\Http\Controllers\InvoicesDiscountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('invoices_discount', InvoicesDiscountController::class);
});
