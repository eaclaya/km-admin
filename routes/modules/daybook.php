<?php

use App\Http\Controllers\FinanceCatalogueController;
use App\Http\Controllers\FinanceDaybookController;
use Illuminate\Support\Facades\Route;

//Nota: El middleware checkPermission se encarga de verificar si el usuario tiene permisos para acceder a la ruta
//Si bien se puede pasar el nombre del permiso directamente (como se ve a continuacion), al no pasarlo se buscara en la base de datos
//el permiso con el nombre de la ruta y se comparara con los permisos del usuario

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
