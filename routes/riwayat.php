<?php

use App\Http\Controllers\Admin\RiwayatController;
use Illuminate\Support\Facades\Route;

// Halaman Riwayat Transaksi — dilindungi auth dan role penjual/super_admin
Route::middleware(['auth', 'role:penjual,super_admin'])->name('riwayat.')->prefix('riwayat')->group(function () {
    Route::get('/', [RiwayatController::class, 'index'])->name('index');
    Route::get('/{riwayat}', [RiwayatController::class, 'show'])->name('show');
});
