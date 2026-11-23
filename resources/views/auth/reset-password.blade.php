<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Atur Ulang Kata Sandi — RM Padang Tiga Putri</title>

    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Cinzel', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        padang: {
                            maroon: '#7B1113',
                            red: '#8A1315',
                            dark: '#5C0B0D',
                            gold: '#C59B27',
                            cream: '#FAF7F2',
                            soft: '#F7EFE5',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF7F2;
        }
        .font-brand-serif {
            font-family: 'Cinzel', Georgia, serif;
        }
        .font-brand-sub {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        input:focus {
            outline: none;
        }
        /* Sembunyikan ikon mata bawaan browser (Edge/IE) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[#FAF7F2] text-stone-800 antialiased flex flex-col justify-between py-6 px-4 sm:px-6">

    <!-- Header Atas: Tombol Kembali ke Login -->
    <div class="w-full max-w-md mx-auto flex items-center justify-start mb-2">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[#8A1315] hover:text-[#5C0B0D] font-bold text-sm transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Login</span>
        </a>
    </div>

    <!-- Main Content Area -->
    <main class="w-full max-w-md mx-auto my-auto flex flex-col items-center">

        <!-- Logo & Identitas Rumah Makan -->
        <div class="text-center mt-1 mb-4">
            <!-- Icon Gonjong Rumah Gadang Khas Minang (Warna Emas) -->
            <div class="flex justify-center mb-1.5">
                <svg viewBox="0 0 200 60" class="h-10 sm:h-11 w-auto" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#C59B27">
                        <path d="M 18,24 Q 44,48 64,15 Q 82,43 100,6 Q 118,43 136,15 Q 156,48 182,24 Q 166,42 154,43 L 46,43 Q 34,42 18,24 Z" />
                        <path d="M 48,45 L 152,45 L 148,54 L 52,54 Z" opacity="0.95" />
                        <rect x="62" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                        <rect x="78" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                        <rect x="97" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                        <rect x="116" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                        <rect x="132" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                    </g>
                </svg>
            </div>

            <!-- Nama Restoran -->
            <h1 class="font-brand-serif font-black text-2xl sm:text-3xl tracking-[0.16em] text-[#7B1113] leading-none uppercase">
                Tiga Putri
            </h1>
            <p class="font-brand-sub text-[10px] sm:text-[11px] font-bold tracking-[0.32em] text-stone-800 uppercase mt-1">
                Masakan Padang
            </p>

            <!-- Judul Halaman -->
            <h2 class="font-brand-serif font-black text-xl sm:text-2xl tracking-wide text-[#7B1113] uppercase mt-3">
                Atur Kata Sandi Baru
            </h2>

            <!-- Ornamen Pembatas Garis Emas & Sendok Garpu -->
            <div class="flex items-center justify-center gap-2 mt-2 max-w-[240px] mx-auto">
                <div class="h-[1.5px] bg-gradient-to-r from-transparent via-[#C59B27] to-[#C59B27] flex-1"></div>
                <div class="flex items-center gap-1.5 text-[#C59B27]">
                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><polygon points="12,2 22,12 12,22 2,12" /></svg>
                    <svg class="w-3.5 h-3.5 text-[#7B1113] fill-current" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-8.03c2.09-.13 3.75-1.85 3.75-3.97V2h-2v7zm8-7c-2.21 0-4 1.79-4 4 0 1.86 1.28 3.41 3 3.86V22h2V9.86c1.72-.45 3-2 3-3.86 0-2.21-1.79-4-4-4z" /></svg>
                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><polygon points="12,2 22,12 12,22 2,12" /></svg>
                </div>
                <div class="h-[1.5px] bg-gradient-to-l from-transparent via-[#C59B27] to-[#C59B27] flex-1"></div>
            </div>

            <p class="text-xs text-stone-600 mt-2.5 max-w-sm mx-auto leading-relaxed">
                Masukkan kode OTP 6 digit yang dikirimkan ke email Anda, lalu buat kata sandi baru.
            </p>
        </div>

        <!-- Kartu Form Atur Kata Sandi Baru -->
        <div class="w-full bg-white rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05),0_8px_10px_-6px_rgba(0,0,0,0.01)] border border-stone-100 p-6 sm:p-7">

            <!-- Flash Session Status / Feedback -->
            @if (session('status'))
                <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- 1. Alamat Email -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                        Alamat Email Pengelola <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <!-- Mail Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $email ?? session('reset_email', '')) }}"
                            required
                            placeholder="nama@email.com"
                            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-[#F9F5EE] hover:bg-[#F5EFE6] focus:bg-white border @error('email') border-red-500 @else border-stone-200 focus:border-[#7B1113] @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 transition"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 2. Kode OTP 6 Digit -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="kode_otp" class="block text-xs sm:text-sm font-bold text-stone-800">
                            Kode OTP (6 Digit) <span class="text-red-600">*</span>
                        </label>
                        <span class="text-[11px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                            Berlaku 10 Menit
                        </span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <!-- Key Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input
                            id="kode_otp"
                            type="text"
                            name="kode_otp"
                            value="{{ old('kode_otp') }}"
                            required
                            maxlength="6"
                            placeholder="Contoh: 123456"
                            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-[#F9F5EE] hover:bg-[#F5EFE6] focus:bg-white border @error('kode_otp') border-red-500 @else border-stone-200 focus:border-[#7B1113] @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm font-semibold tracking-widest focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 transition"
                        >
                    </div>
                    @error('kode_otp')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 3. Kata Sandi Baru -->
                <div>
                    <label for="password" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                        Kata Sandi Baru <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <!-- Lock Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-[#F9F5EE] hover:bg-[#F5EFE6] focus:bg-white border @error('password') border-red-500 @else border-stone-200 focus:border-[#7B1113] @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 transition"
                        >
                        <!-- Toggle Password Button -->
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('password', 'eyePasswordOpen', 'eyePasswordClosed')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none"
                            aria-label="Tampilkan / Sembunyikan Kata Sandi"
                        >
                            <svg id="eyePasswordOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyePasswordClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 4. Konfirmasi Kata Sandi Baru -->
                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                        Konfirmasi Kata Sandi Baru <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <!-- Shield Check Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi baru"
                            class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-[#F9F5EE] hover:bg-[#F5EFE6] focus:bg-white border @error('password_confirmation') border-red-500 @else border-stone-200 focus:border-[#7B1113] @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 transition"
                        >
                        <!-- Toggle Password Confirmation Button -->
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('password_confirmation', 'eyeConfirmOpen', 'eyeConfirmClosed')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none"
                            aria-label="Tampilkan / Sembunyikan Konfirmasi Kata Sandi"
                        >
                            <svg id="eyeConfirmOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeConfirmClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Info Peringatan Keamanan -->
                <div class="p-3 bg-amber-50/80 border border-amber-200/90 rounded-xl text-xs text-amber-900 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="leading-relaxed">
                        Demi keamanan akun, kata sandi baru <strong>tidak boleh sama</strong> dengan kata sandi lama Anda dan wajib minimal 8 karakter.
                    </span>
                </div>

                <!-- Tombol Submit Simpan Kata Sandi Baru -->
                <button
                    type="submit"
                    class="w-full mt-2 py-3 px-4 bg-[#8A1315] hover:bg-[#700D0F] active:bg-[#5C0B0D] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm sm:text-base group cursor-pointer"
                >
                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Kata Sandi Baru</span>
                </button>
            </form>

            <!-- Tombol Kirim Ulang OTP -->
            <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-600">
                <span>Belum terima kode OTP?</span>
                <form method="POST" action="{{ route('password.email') }}" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email', $email ?? session('reset_email', '')) }}">
                    <button type="submit" class="font-bold text-[#8A1315] hover:text-[#5C0B0D] hover:underline cursor-pointer">
                        Kirim Ulang Kode OTP
                    </button>
                </form>
            </div>
        </div>

    </main>

    <!-- Footer Khas Rumah Makan Padang Tiga Putri -->
    <footer class="w-full max-w-md mx-auto text-center mt-6 text-stone-500">
        <div class="flex items-center justify-center gap-3 text-stone-400 mb-1.5">
            <svg class="w-4 h-4 fill-[#C59B27]" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v2h-2v-2zm0-10h2v8h-2V7z"/></svg>
            <span class="text-[11px] font-medium text-stone-500">Verifikasi Aman melalui SMTP Email</span>
        </div>
        <p class="text-xs font-semibold text-stone-700">
            Rumah Makan Padang Tiga Putri — Sukatani, Bekasi
        </p>
        <p class="text-[11px] text-stone-500 mt-0.5">
            Warisan Resep Bukittinggi & Payakumbuh • Sejak 2009
        </p>
    </footer>

    <!-- JavaScript Toggle Show/Hide Password -->
    <script>
        function togglePasswordVisibility(inputId, openIconId, closedIconId) {
            const input = document.getElementById(inputId);
            const openIcon = document.getElementById(openIconId);
            const closedIcon = document.getElementById(closedIconId);

            if (input.type === 'password') {
                input.type = 'text';
                openIcon.classList.add('hidden');
                closedIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                openIcon.classList.remove('hidden');
                closedIcon.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
