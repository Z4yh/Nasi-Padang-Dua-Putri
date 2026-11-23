<?php

use App\Http\Controllers\Admin\KateringController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:penjual,super_admin'])->prefix('katering')->name('katering.')->group(function () {
    Route::get('/', [KateringController::class, 'index'])->name('index');
    Route::post('/', [KateringController::class, 'store'])->name('store');
    Route::put('/{kateringOrder}', [KateringController::class, 'update'])->name('update');
    Route::patch('/{kateringOrder}/status', [KateringController::class, 'updateStatus'])->name('status');
    Route::patch('/{kateringOrder}/payment', [KateringController::class, 'confirmPayment'])->name('payment');
    Route::patch('/{kateringOrder}/complete', [KateringController::class, 'markCompleted'])->name('complete');
    Route::patch('/{kateringOrder}/archive', [KateringController::class, 'archive'])->name('archive');
    Route::get('/{kateringOrder}/nota', [KateringController::class, 'nota'])->name('nota');
    Route::get('/export/csv', [KateringController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [KateringController::class, 'exportPdf'])->name('export.pdf');
});