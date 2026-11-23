<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'whatsapp' => ['required', 'string', 'max:25'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', 'unique:tigaputri_md_users,username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'username.required' => 'Username pengelola / ID staf wajib diisi.',
            'username.unique' => 'Username pengelola ini sudah terdaftar.',
            'username.alpha_dash' => 'Username hanya boleh huruf kecil, angka, tanda strip (-) dan garis bawah (_).',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Standar Operasional RM Padang Tiga Putri.',
        ]);

        // Email dibuat otomatis berbasis username jika tidak diisi langsung
        $email = $request->email ?: ($validated['username'] . '@tigaputri.local');

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'username' => $validated['username'],
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => 'penjual', // Mendaftar sebagai akun pengelola/penjual
            'status_aktif' => 1,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $user->nama_lengkap . '.');
    }
}
