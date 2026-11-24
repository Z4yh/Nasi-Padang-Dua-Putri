<?php

use App\Http\Controllers\Api\FinanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute API Keuangan (Finance API)
|--------------------------------------------------------------------------
|
| Endpoint untuk Laporan Arus Kas, Ringkasan, Riwayat, dan CRUD Pengeluaran.
| Dilindungi autentikasi dan otorisasi khusus peran Admin (penjual & super_admin).
|
*/

Route::prefix('api/finance')
    ->name('api.finance.')
    ->middleware(['web', 'auth', 'role:admin'])
    ->group(function () {
        Route::get('/summary', [FinanceController::class, 'summary'])->name('summary');
        Route::get('/entries', [FinanceController::class, 'entries'])->name('entries');
        Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store');
        Route::patch('/expenses/{id}', [FinanceController::class, 'updateExpense'])->name('expenses.update');
        Route::delete('/expenses/{id}', [FinanceController::class, 'destroyExpense'])->name('expenses.destroy');
    });
