<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan formulir permintaan kode OTP lupa kata sandi.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Tangani pengiriman kode OTP ke email pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Alamat email ini tidak terdaftar dalam sistem pengelola kami.']);
        }

        // 1. Generate 6 digit angka OTP acak
        $otp = (string) random_int(100000, 999999);

        // 2. Bersihkan kode OTP lama yang belum terpakai untuk user ini
        EmailOtp::where('id_user', $user->id_user)->delete();

        // 3. Simpan OTP baru ke database (berlaku 10 menit)
        EmailOtp::create([
            'id_user' => $user->id_user,
            'kode_otp' => $otp,
            'waktu_kadaluarsa' => now()->addMinutes(10),
        ]);

        // 4. Kirim email notifikasi berisi kode OTP melalui SMTP
        try {
            $user->notify(new SendOtpNotification($otp));
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Gagal mengirim email OTP: ' . $e->getMessage() . '. Pastikan pengaturan SMTP sudah aktif.']);
        }

        // Simpan email ke session untuk memudahkan pengguna di langkah reset kata sandi
        session(['reset_email' => $user->email]);

        return redirect()->route('password.reset', ['email' => $user->email])
            ->with('status', 'Kode verifikasi OTP 6 digit telah dikirim ke ' . $user->email . '. Silakan periksa kotak masuk atau spam email Anda.');
    }
}

