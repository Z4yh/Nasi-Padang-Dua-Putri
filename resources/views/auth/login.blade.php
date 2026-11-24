<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Pengelola — RM Padang Tiga Putri</title>

    <!-- Google Fonts: Cinzel (Serif Tradisional Minang) & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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
        /* Custom scrollbar & smoothness */
        input:focus {
            outline: none;
        }
        /* Sembunyikan ikon mata bawaan browser (Edge/IE) agar tidak dobel dengan tombol toggle kustom */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-[#FAF7F2] text-stone-800 antialiased flex flex-col justify-between py-6 px-4 sm:px-6">

    <!-- Header Atas: Tombol Kembali -->
    <div class="w-full max-w-md mx-auto flex items-center justify-start">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-[#8A1315] hover:text-[#5C0B0D] font-bold text-sm transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Main Content Area -->
    <main class="w-full max-w-md mx-auto my-auto flex flex-col items-center">

        <!-- Logo & Identitas Rumah Makan -->
        <div class="text-center mt-2 mb-4">
            <!-- Icon Gonjong Rumah Gadang Khas Minang (Warna Emas) -->
            <div class="flex justify-center mb-1.5">
                <svg viewBox="0 0 200 60" class="h-10 sm:h-11 w-auto" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#C59B27">
                        <!-- 5 Pucuk Gonjong Minangkabau -->
                        <path d="M 18,24 Q 44,48 64,15 Q 82,43 100,6 Q 118,43 136,15 Q 156,48 182,24 Q 166,42 154,43 L 46,43 Q 34,42 18,24 Z" />
                        <!-- Dinding Panggung Rumah Gadang -->
                        <path d="M 48,45 L 152,45 L 148,54 L 52,54 Z" opacity="0.95" />
                        <!-- Jendela / Ornamen Ukiran Minang -->
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
            <p class="text-[10px] sm:text-[11px] font-bold tracking-[0.32em] text-stone-800 uppercase mt-1">
                Masakan Padang
            </p>

            <!-- Judul Halaman -->
            <h2 class="font-brand-serif font-black text-xl sm:text-2xl tracking-[0.12em] text-[#7B1113] uppercase mt-4">
                Login Pengelola
            </h2>

            <!-- Ornamen Pembatas Garis Emas & Sendok Garpu -->
            <div class="flex items-center justify-center gap-2 mt-2 max-w-[240px] mx-auto">
                <div class="h-[1.5px] bg-gradient-to-r from-transparent via-[#C59B27] to-[#C59B27] flex-1"></div>
                <div class="flex items-center gap-1.5 text-[#C59B27]">
                    <!-- Berlian Emas Kiri -->
                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                        <polygon points="12,2 22,12 12,22 2,12" />
                    </svg>
                    <!-- Garpu & Sendok Maroon -->
                    <svg class="w-3.5 h-3.5 text-[#7B1113] fill-current" viewBox="0 0 24 24">
                        <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-8.03c2.09-.13 3.75-1.85 3.75-3.97V2h-2v7zm8-7c-2.21 0-4 1.79-4 4 0 1.86 1.28 3.41 3 3.86V22h2V9.86c1.72-.45 3-2 3-3.86 0-2.21-1.79-4-4-4z" />
                    </svg>
                    <!-- Berlian Emas Kanan -->
                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                        <polygon points="12,2 22,12 12,22 2,12" />
                    </svg>
                </div>
                <div class="h-[1.5px] bg-gradient-to-l from-transparent via-[#C59B27] to-[#C59B27] flex-1"></div>
            </div>
        </div>

        <!-- Kartu Form Login -->
        <div class="w-full bg-white rounded-2xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05),0_8px_10px_-6px_rgba(0,0,0,0.01)] border border-stone-100 p-6 sm:p-7">

            <!-- Flash Session Status / Feedback -->
            @if (session('status'))
                <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Input Username / ID Pengelola -->
                <div>
                    <label for="username" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                        Username / ID Pengelola <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <!-- User Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Contoh: tigaputri.owner"
                            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-stone-50/50 hover:bg-white focus:bg-white border @error('username') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                        >
                    </div>
                    @error('username')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Input Kata Sandi (Password) -->
                <div>
                    <label for="password" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                        Kata Sandi (Password) <span class="text-red-600">*</span>
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
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-stone-50/50 hover:bg-white focus:bg-white border @error('password') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 placeholder-stone-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                        >
                        <!-- Toggle Show/Hide Password -->
                        <button
                            type="button"
                            onclick="togglePassword()"
                            id="togglePasswordBtn"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none"
                            aria-label="Tampilkan / Sembunyikan Kata Sandi"
                        >
                            <!-- Eye Outline Icon (Default) -->
                            <svg id="eyeOpenIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Off Icon (Hidden by default) -->
                            <svg id="eyeClosedIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Lupa Sandi -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-stone-300 text-[#7B1113] focus:ring-[#7B1113] accent-[#7B1113] cursor-pointer"
                        >
                        <span class="ml-2 text-xs sm:text-sm font-medium text-stone-800">
                            Ingat di Tablet/HP
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs sm:text-sm font-bold text-[#7B1113] hover:text-[#5C0B0D] hover:underline transition-colors">
                            Lupa Sandi?
                        </a>
                    @endif
                </div>

                <!-- Tombol Masuk -->
                <button
                    type="submit"
                    class="w-full mt-4 py-3 px-4 bg-[#8A1315] hover:bg-[#700D0F] active:bg-[#5C0B0D] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm sm:text-base group"
                >
                    
                    <span>Masuk</span>
                </button>
            </form>

            <!-- Garis Pembatas "Belum punya akun?" -->
            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-stone-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-stone-500 font-medium">
                        Belum punya akun?
                    </span>
                </div>
            </div>

            <!-- Tombol Daftar Akun -->
            @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="w-full py-2.5 sm:py-3 px-4 bg-[#F7EFE5] hover:bg-[#EFE3D5] active:bg-[#E6D7C5] text-[#7B1113] font-bold rounded-xl transition duration-150 flex items-center justify-center gap-2 text-xs sm:text-sm text-center"
                >
                    <svg class="w-4 h-4 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Daftar Akun</span>
                </a>
            @endif

        </div>

    </main>

    <!-- Footer Bawah -->
    <footer class="w-full max-w-md mx-auto text-center mt-6 pt-2 pb-2">
        <!-- 3 Icon Ornamen Kuliner -->
        <div class="flex items-center justify-center gap-3 text-stone-500 mb-2">
            <!-- Icon 1: Sajian Makanan / Nasi Padang / Piring Tudung -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v2m-8 7a8 8 0 0116 0H4zm-1 3h18a1 1 0 011 1v1a2 2 0 01-2 2H4a2 2 0 01-2-2v-1a1 1 0 011-1z" />
            </svg>
            <!-- Icon 2: Wajan Masak Gulai / Rendang Beruap -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m-4-2v2m8-2v2M4 11h16a1 1 0 011 1v2a6 6 0 01-6 6h-6a6 6 0 01-6-6v-2a1 1 0 011-1zm16 2h3" />
            </svg>
            <!-- Icon 3: Cangkir Kopi Teh Talua / Minuman -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-5v3m4-3v3m4-3v3" />
            </svg>
        </div>

        <!-- Info Alamat & Warisan Resep -->
        <p class="text-xs font-semibold text-stone-700">
            Rumah Makan Padang Tiga Putri — Sukatani, Bekasi
        </p>
        <p class="text-[11px] text-stone-400 mt-0.5">
            Warisan Resep Bukittinggi & Payakumbuh • Sejak 2009
        </p>
    </footer>

    <!-- Script Toggle Password Visibility -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpenIcon');
            const eyeClosed = document.getElementById('eyeClosedIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
