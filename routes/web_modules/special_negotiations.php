<?php

use App\Http\Controllers\SpecialNegotiationsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::post('special_negotiations/quota/store', [SpecialNegotiationsController::class, 'quotaStore'])->name('special_negotiations.quota.store');
    Route::post('special_negotiations/quota/{id}/update', [SpecialNegotiationsController::class, 'quotaUpdate'])->name('special_negotiations.quota.update');

    Route::post('special_negotiations/payment/store', [SpecialNegotiationsController::class, 'paymentStore'])->name('special_negotiations.payment.store');
    Route::post('special_negotiations/payment/{id}/update', [SpecialNegotiationsController::class, 'paymentUpdate'])->name('special_negotiations.payment.update');

    Route::post('special_negotiations/refund/store', [SpecialNegotiationsController::class, 'refundStore'])->name('special_negotiations.refund.store');
    Route::post('special_negotiations/refund/{id}/update', [SpecialNegotiationsController::class, 'refundUpdate'])->name('special_negotiations.refund.update');

    Route::get('special_negotiations/{id}/get_payments', [SpecialNegotiationsController::class, 'get_payments'])->name('special_negotiations.get_payments');
    Route::get('special_negotiations/{id}/get_refunds', [SpecialNegotiationsController::class, 'get_refunds'])->name('special_negotiations.get_refunds');

    Route::resource('special_negotiations', SpecialNegotiationsController::class);
});
