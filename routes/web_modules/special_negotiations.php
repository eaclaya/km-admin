<?php

use App\Http\Controllers\SpecialNegotiationsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::post('special_negotiations/quota/store', [SpecialNegotiationsController::class, 'quotaStore'])
        ->name('special_negotiations.quota.store');
    Route::post('special_negotiations/quota/{id}/update', [SpecialNegotiationsController::class, 'quotaUpdate'])
        ->name('special_negotiations.quota.update');

    Route::post('special_negotiations/payment/store', [SpecialNegotiationsController::class, 'paymentStore'])
        ->name('special_negotiations.payment.store');
    Route::post('special_negotiations/payment/{id}/update', [SpecialNegotiationsController::class, 'paymentUpdate'])
        ->name('special_negotiations.payment.update');
    Route::post('special_negotiations/payment/{id}/destroy', [SpecialNegotiationsController::class, 'paymentDestroy'])
        ->name('special_negotiations.payment.destroy');

    Route::post('special_negotiations/refund/store', [SpecialNegotiationsController::class, 'refundStore'])
        ->name('special_negotiations.refund.store');
    Route::post('special_negotiations/refund/{id}/update', [SpecialNegotiationsController::class, 'refundUpdate'])
        ->name('special_negotiations.refund.update');
    Route::post('special_negotiations/refund/{id}/destroy', [SpecialNegotiationsController::class, 'refundDestroy'])
        ->name('special_negotiations.refund.destroy');

    Route::post('special_negotiations/discount/store', [SpecialNegotiationsController::class, 'discountStore'])
        ->name('special_negotiations.discount.store');
    Route::post('special_negotiations/discount/{id}/update', [SpecialNegotiationsController::class, 'discountUpdate'])
        ->name('special_negotiations.discount.update');
    Route::post('special_negotiations/discount/{id}/destroy', [SpecialNegotiationsController::class, 'discountDestroy'])
        ->name('special_negotiations.discount.destroy');

    Route::get('special_negotiations/{id}/get_payments', [SpecialNegotiationsController::class, 'get_payments'])
        ->name('special_negotiations.get_payments');
    Route::get('special_negotiations/{id}/get_refunds', [SpecialNegotiationsController::class, 'get_refunds'])
        ->name('special_negotiations.get_refunds');

    Route::get('special_negotiations/{id}/set_credit_record', [SpecialNegotiationsController::class, 'set_credit_record'])
        ->name('special_negotiations.set_credit_record');

    Route::get('special_negotiations/{id}/set_document', [SpecialNegotiationsController::class, 'set_document'])
        ->name('special_negotiations.set_document');

    Route::resource('special_negotiations', SpecialNegotiationsController::class);
});
