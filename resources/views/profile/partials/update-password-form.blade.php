<section x-data="{
    showNewPass: false,
    showConfirmPass: false,
    otpSent: {{ session('otp_sent') ? 'true' : 'false' }},
    countdown: {{ session('otp_sent') ? '300' : '0' }},
    timer: null,
    startCountdown() {
        if (this.countdown > 0) {
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    clearInterval(this.timer);
                }
            }, 1000);
        }
    },
    formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }
}" x-init="if (otpSent) startCountdown()">

    <!-- Header Section -->
    <header class="flex items-center gap-3 pb-4 border-b border-stone-100">
        <div class="w-10 h-10 rounded-xl bg-[#FAF7F2] border border-[#EAE3D9] flex items-center justify-center text-[#7B1113] shrink-0">
            <!-- Shield Lock Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-stone-900">
                Ganti Kata Sandi (Verifikasi OTP)
            </h2>
            <p class="text-xs sm:text-sm text-stone-500">
                Amankan akun Anda dengan mengganti kata sandi menggunakan kode OTP 6 digit yang dikirim ke email.
            </p>
        </div>
    </header>

    <!-- Notifikasi Sukses / Error / Info -->
    @if (session('error_otp'))
        <div class="mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs sm:text-sm font-semibold text-red-800 flex items-start gap-2">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p>{{ session('error_otp') }}</p>
            </div>
        </div>
    @endif

    @if (session('success_password'))
        <div class="mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm font-semibold text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success_password') }}</span>
        </div>
    @endif

    @if (session('success_otp'))
        <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs sm:text-sm font-semibold text-amber-800 flex items-start gap-2">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div>
                <p>{{ session('success_otp') }}</p>
            </div>
        </div>
    @endif

    <!-- Form Tersembunyi untuk Request OTP -->
    <form id="send-otp-form" method="post" action="{{ route('profile.send-otp') }}" class="hidden">
        @csrf
    </form>

    <!-- Langkah 1: Pengiriman Kode OTP -->
    <div class="mt-6 p-4 rounded-2xl bg-[#FAF7F2] border border-[#EAE3D9] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-0.5">
            <span class="text-xs font-bold uppercase tracking-wider text-[#7B1113]">Langkah 1</span>
            <h3 class="text-sm font-bold text-stone-800">Kirim Kode Verifikasi ke Email</h3>
            <p class="text-xs text-stone-500">
                Kode OTP akan dikirimkan ke alamat email: <strong class="text-stone-800">{{ $user->email }}</strong>
            </p>
        </div>

        <div>
            <!-- Tombol Kirim OTP / Hitung Mundur -->
            <button
                type="submit"
                form="send-otp-form"
                :disabled="countdown > 0"
                :class="countdown > 0 ? 'bg-stone-200 text-stone-500 cursor-not-allowed' : 'bg-[#7B1113] hover:bg-[#630E10] text-white shadow-xs'"
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition duration-150 inline-flex items-center justify-center gap-2 whitespace-nowrap"
            >
                <!-- Mail Icon -->
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>

                <template x-if="countdown > 0">
                    <span>Kirim Ulang (<span x-text="formatTime(countdown)"></span>)</span>
                </template>
                <template x-if="countdown === 0">
                    <span>{{ session('otp_sent') ? 'Kirim Ulang Kode OTP' : 'Kirim Kode OTP' }}</span>
                </template>
            </button>
        </div>
    </div>

    <!-- Langkah 2: Form Input OTP & Kata Sandi Baru -->
    <form method="post" action="{{ route('profile.password.otp') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <!-- Input Kode OTP -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="kode_otp" class="block text-xs sm:text-sm font-bold text-stone-800">
                    Kode OTP (6 Digit) <span class="text-red-600">*</span>
                </label>
                <span class="text-[11px] text-stone-500">Periksa kotak masuk / spam email Anda</span>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <!-- Keypad icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 8.167" />
                    </svg>
                </div>
                <input
                    id="kode_otp"
                    name="kode_otp"
                    type="text"
                    maxlength="6"
                    inputmode="numeric"
                    placeholder="Contoh: 849201"
                    value="{{ old('kode_otp') }}"
                    required
                    class="w-full pl-10 pr-3.5 py-2.5 bg-stone-50/50 hover:bg-white focus:bg-white border @error('kode_otp') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 font-mono tracking-widest text-base focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                />
            </div>
            @error('kode_otp')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Input Kata Sandi Baru -->
        <div>
            <label for="new_password" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                Kata Sandi Baru <span class="text-red-600">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input
                    id="new_password"
                    name="password"
                    :type="showNewPass ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter"
                    required
                    autocomplete="new-password"
                    class="w-full pl-10 pr-11 py-2.5 bg-stone-50/50 hover:bg-white focus:bg-white border @error('password') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                />
                <button
                    type="button"
                    @click="showNewPass = !showNewPass"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none"
                    aria-label="Tampilkan / Sembunyikan Kata Sandi"
                >
                    <svg x-show="!showNewPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showNewPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Input Konfirmasi Kata Sandi Baru -->
        <div>
            <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                Konfirmasi Kata Sandi Baru <span class="text-red-600">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showConfirmPass ? 'text' : 'password'"
                    placeholder="Ulangi kata sandi baru"
                    required
                    autocomplete="new-password"
                    class="w-full pl-10 pr-11 py-2.5 bg-stone-50/50 hover:bg-white focus:bg-white border border-stone-200 rounded-xl text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                />
                <button
                    type="button"
                    @click="showConfirmPass = !showConfirmPass"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none"
                    aria-label="Tampilkan / Sembunyikan Konfirmasi Kata Sandi"
                >
                    <svg x-show="!showConfirmPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showConfirmPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Tombol Simpan Password Baru -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full sm:w-auto px-6 py-2.5 bg-[#8A1315] hover:bg-[#720E10] active:bg-[#5C0B0D] text-white font-bold text-sm rounded-xl shadow-xs hover:shadow-md transition duration-150 inline-flex items-center justify-center gap-2"
            >
                <!-- Lock check icon -->
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Simpan & Perbarui Kata Sandi</span>
            </button>
        </div>
    </form>
</section>
