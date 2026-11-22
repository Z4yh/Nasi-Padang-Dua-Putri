<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\EmailOtp;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman pengaturan profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui informasi profil pengguna (Nama & Email).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Kirimkan 6 digit kode OTP ke email pengguna untuk verifikasi ganti password.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Generate 6 digit angka OTP acak
        $otp = (string) random_int(100000, 999999);

        // 2. Bersihkan kode OTP lama yang belum terpakai untuk user ini
        EmailOtp::where('id_user', $user->id_user)->delete();

        // 3. Simpan OTP baru ke database dengan waktu kadaluarsa 5 menit
        EmailOtp::create([
            'id_user' => $user->id_user,
            'kode_otp' => $otp,
            'waktu_kadaluarsa' => now()->addMinutes(5),
        ]);

        // 4. Kirim notifikasi email berisi kode OTP
        try {
            $user->notify(new SendOtpNotification($otp));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error_otp', 'Gagal mengirim email: ' . $e->getMessage() . '. Pastikan pengaturan SMTP pada file .env sudah terisi dengan benar.');
        }

        $successMsg = 'Kode OTP 6 digit telah dikirim langsung ke email ' . $user->email . '. Silakan periksa kotak masuk atau spam email Anda.';

        return back()->with('otp_sent', true)
                     ->with('success_otp', $successMsg);
    }

    /**
     * Perbarui kata sandi pengguna dengan validasi kode OTP email.
     */
    public function updatePasswordWithOtp(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'kode_otp' => ['required', 'string', 'size:6'],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'kode_otp.required' => 'Kode OTP wajib diisi.',
            'kode_otp.size' => 'Kode OTP harus berupa 6 digit angka.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Verifikasi kode OTP di database
        $otpRecord = EmailOtp::where('id_user', $user->id_user)
            ->where('kode_otp', $request->kode_otp)
            ->where('waktu_kadaluarsa', '>=', now())
            ->first();

        if (! $otpRecord) {
            return back()->withErrors([
                'kode_otp' => 'Kode OTP tidak valid atau sudah kadaluarsa (melewati 5 menit). Silakan minta kode baru.',
            ])->withInput();
        }

        // Simpan kata sandi baru yang telah di-hash
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus record OTP setelah berhasil digunakan
        $otpRecord->delete();

        return back()->with('success_password', 'Kata sandi akun Anda berhasil diperbarui dengan aman!');
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
