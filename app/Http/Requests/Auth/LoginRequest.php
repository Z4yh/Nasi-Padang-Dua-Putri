<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input login.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Kustomisasi pesan error validasi.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Proses autentikasi fleksibel (Username atau Email).
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = $this->input('username');

        // Deteksi otomatis apakah input berupa email atau username
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field     => $loginInput,
            'password' => $this->input('password'),
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'Username/email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Batasi percobaan login gagal (Maksimal 5 kali percobaan).
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Kunci throttle rate limiting berbasis username dan IP pengguna.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')) . '|' . $this->ip());
    }
}