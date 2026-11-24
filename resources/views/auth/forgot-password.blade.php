<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Kata Sandi — RM Padang Tiga Putri</title>

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

  
        <div class="text-center mt-1 mb-4">
            <!-- Icon Gonjong Rumah Gadang Khas Minang (Warna Emas) -->
            <div class="flex justify-center mb-1.5">
                
            </div>

           

            <!-- Judul Halaman -->
            <h2 class="font-brand-serif font-black text-xl sm:text-2xl tracking-wide text-[#7B1113] uppercase mt-3">
                Lupa Kata Sandi
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
                Masukkan alamat email akun pengelola Anda. Kami akan mengirimkan <strong>6 digit kode OTP</strong> untuk verifikasi pergantian kata sandi.
            </p>
        </div>

        <!-- Kartu Form Lupa Password -->
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

            @if (session('error'))
                <div class="mb-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-semibold text-red-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Input Alamat Email -->
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
                            value="{{ old('email', session('reset_email', '')) }}"
                            required
                            autofocus
                            placeholder="nama@email.com"
                            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-stone-50/50 hover:bg-white focus:bg-white border @error('email') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                        >
                    </div>
                    <p class="text-[11px] text-stone-500 mt-1.5">
                        Kode OTP 6 digit akan dikirimkan ke alamat email ini.
                    </p>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tombol Kirim Kode OTP -->
                <button
                    type="submit"
                    class="w-full mt-2 py-3 px-4 bg-[#8A1315] hover:bg-[#700D0F] active:bg-[#5C0B0D] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm sm:text-base group cursor-pointer"
                >
                    <span>Kirim Kode OTP</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Tautan alternatif jika sudah punya OTP -->
            <div class="mt-5 pt-4 border-t border-stone-100 text-center">
                <p class="text-xs text-stone-600">
                    Sudah memiliki kode OTP verifikasi?
                    <a href="{{ route('password.reset') }}" class="font-bold text-[#8A1315] hover:text-[#5C0B0D] hover:underline transition-colors block sm:inline mt-1 sm:mt-0">
                        Atur Kata Sandi Baru di sini
                    </a>
                </p>
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

</body>
</html>
