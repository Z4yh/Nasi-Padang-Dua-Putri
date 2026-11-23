<?php

use App\Http\Controllers\KategoriMenuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. RUTE PUBLIK (Guest / Tidak Perlu Login)
// =========================================================================
Route::get('/', function () {
    return view('welcome');
})->name('beranda');

Route::get('/home', fn () => redirect()->route('beranda'));

// Tampilan Menu Publik (Pengunjung/Guest dapat mengakses tanpa login)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// =========================================================================
// 2. RUTE DASAR TERAUTENTIKASI (Perlu Login)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Alias admin.dashboard yang digunakan oleh AuthenticatedSessionController
    Route::get('/admin/dashboard', fn () => redirect()->route('dashboard'))->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/send-otp', [ProfileController::class, 'sendOtp'])->name('profile.send-otp');
    Route::put('/profile/password-otp', [ProfileController::class, 'updatePasswordWithOtp'])->name('profile.password.otp');
});

// =========================================================================
// 3. RUTE KHUSUS ADMIN / PENJUAL & SUPER ADMIN (Middleware: role:penjual,super_admin)
// =========================================================================
Route::middleware(['auth', 'role:penjual,super_admin'])->group(function () {
    // Katalog & Kategori Menu
    Route::resource('kategori-menu', KategoriMenuController::class)->except(['show']);
    Route::patch('menu/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menu.toggle-status');
    Route::resource('menu', MenuController::class)->except(['index', 'show']);

    // Modul Operasional Penjual & Admin
    Route::get('/katering', function () {
        return view('dashboard', ['title' => 'Manajemen Katering']);
    })->name('katering.index');

    Route::get('/kasir', function () {
        return view('dashboard', ['title' => 'Sistem Kasir & Transaksi']);
    })->name('kasir.index');

    Route::get('/keuangan', function () {
        return view('dashboard', ['title' => 'Laporan Keuangan & Pengeluaran']);
    })->name('keuangan.index');
});

// =========================================================================
// 4. RUTE KHUSUS SUPER ADMIN (Middleware: role:super_admin)
// =========================================================================
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // CMS Konten Web (Beranda & Menu Favorit)
    Route::get('/cms', function () {
        return view('dashboard', ['title' => 'CMS Manajemen Konten Web']);
    })->name('cms.index');

    // Master Data Lanjutan
    Route::get('/master-data', function () {
        return redirect()->route('kategori-menu.index');
    })->name('master-data.index');

    // Kelola Akun & Hak Akses Pengguna
    Route::get('/kelola-akun', function () {
        return view('dashboard', ['title' => 'Kelola Akun & Hak Akses Pengguna']);
    })->name('kelola-akun.index');
});

require __DIR__.'/auth.php';
