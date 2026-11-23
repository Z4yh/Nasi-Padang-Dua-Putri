<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Tampilkan formulir atur ulang kata sandi dengan kode OTP.
     */
    public function create(Request $request): View
    {
        $email = $request->query('email') ?? session('reset_email') ?? old('email');

        return view('auth.reset-password', [
            'request' => $request,
            'email' => $email,
        ]);
    }

    /**
     * Tangani permintaan pembaruan kata sandi menggunakan OTP dan deteksi kata sandi lama.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'kode_otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'kode_otp.required' => 'Kode OTP 6 digit wajib diisi.',
            'kode_otp.size' => 'Kode OTP harus berupa 6 digit angka.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Alamat email tidak terdaftar dalam sistem pengelola kami.']);
        }

        // 1. Verifikasi kode OTP di tabel tigaputri_md_email_otp
        $otpRecord = EmailOtp::where('id_user', $user->id_user)
            ->where('kode_otp', $request->kode_otp)
            ->where('waktu_kadaluarsa', '>=', now())
            ->first();

        if (! $otpRecord) {
            return back()->withInput($request->only('email', 'kode_otp'))
                ->withErrors(['kode_otp' => 'Kode OTP salah atau sudah kadaluarsa (lebih dari 10 menit). Silakan minta kode baru.']);
        }

        // 2. DETEKSI APAKAH PASSWORD BARU SAMA DENGAN PASSWORD LAMA
        if (Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email', 'kode_otp'))
                ->withErrors([
                    'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama Anda. Silakan gunakan kombinasi kata sandi baru yang berbeda demi keamanan akun.',
                ]);
        }

        // 3. Simpan kata sandi baru
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // 4. Hapus OTP yang sudah terpakai
        $otpRecord->delete();
        session()->forget('reset_email');

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', 'Kata sandi akun Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}

