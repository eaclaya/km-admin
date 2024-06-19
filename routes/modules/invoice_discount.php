<?php

use App\Http\Controllers\InvoiceDiscountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('invoice_discount/export_invoice_pdf', [InvoiceDiscountController::class, 'exportInvoicesPdf'])
        ->name('invoice_discount.export_invoice_pdf');

    Route::post('invoice_discount/set_discount', [InvoiceDiscountController::class, 'setDiscount'])
        ->name('invoice_discount.set_discount');

    Route::post('invoice_discount/export_invoice', [InvoiceDiscountController::class, 'exportInvoicesCsv'])
        ->name('invoice_discount.export_invoice');
    Route::get('invoice_discount/export_invoice', [InvoiceDiscountController::class, 'exportInvoicesCsv'])
        ->name('invoice_discount.export_invoice');

    Route::get('reports/finish_report', [InvoiceDiscountController::class, 'finishReport'])
        ->name('reports.finish_report');

    Route::get('invoice_discount/{account_id?}', [InvoiceDiscountController::class, 'index'])
        ->name('invoice_discount.index');
});
