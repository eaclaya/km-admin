<?php

use App\Http\Controllers\SpecialNegotiationsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::post('special_negotiations/quota/store', [SpecialNegotiationsController::class, 'quotaStore'])->name('special_negotiations.quota.store');
    Route::post('special_negotiations/payment/store', [SpecialNegotiationsController::class, 'paymentStore'])->name('special_negotiations.payment.store');

    Route::post('special_negotiations/quota/{id}/update', [SpecialNegotiationsController::class, 'quotaUpdate'])->name('special_negotiations.quota.update');

    Route::get('special_negotiations/{id}/get_payments', [SpecialNegotiationsController::class, 'get_payments'])->name('special_negotiations.get_payments');
    Route::resource('special_negotiations', SpecialNegotiationsController::class);
});
