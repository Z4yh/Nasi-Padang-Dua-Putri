<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan form login (Login-Admin).
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi masuk dengan pengecekan status aktif.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Eksekusi autentikasi bawaan LoginRequest
        $request->authenticate();

        $user = Auth::user();

        // 2. Cegah akses jika akun berstatus nonaktif (status_aktif != 1)
        if ($user->status_aktif !== 1) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Akun Anda dinonaktifkan oleh Super Admin. Silakan hubungi Super Admin.',
            ]);
        }

        // 3. Regenerasi session ID untuk mencegah session fixation
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false))
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Proses keluar (Logout) dan pembersihan sesi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}