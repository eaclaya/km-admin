<?php

use App\Http\Controllers\FinanceCatalogueController;
use App\Http\Controllers\FinanceDaybookController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','checkPermission:whatsapp_config'])->group(function () {
    Route::get('finance_catalogue/show_classifications', [FinanceCatalogueController::class, 'showClassifications'])
        ->name('finance_catalogue.show_classifications');
    Route::post('finance_catalogue/set_classifications', [FinanceCatalogueController::class, 'setClassifications'])
        ->name('finance_catalogue.set_classifications');
    Route::get('finance_catalogue/get_models', [FinanceCatalogueController::class, 'getModels'])
        ->name('finance_catalogue.get_models');
    Route::get('finance_catalogue/export', [FinanceCatalogueController::class, 'export'])
        ->name('finance_catalogue.export');

    Route::resource('finance_catalogue', FinanceCatalogueController::class);

    Route::resource('finance_daybook', FinanceDaybookController::class);
});
