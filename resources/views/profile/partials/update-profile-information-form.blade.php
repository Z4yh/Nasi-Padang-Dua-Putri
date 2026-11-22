<section>
    <header class="flex items-center gap-3 pb-4 border-b border-stone-100">
        <div class="w-10 h-10 rounded-xl bg-[#FAF7F2] border border-[#EAE3D9] flex items-center justify-center text-[#7B1113] shrink-0">
            <!-- User Pen Icon -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-stone-900">
                Informasi Profil
            </h2>
            <p class="text-xs sm:text-sm text-stone-500">
                Perbarui nama lengkap dan alamat email yang digunakan akun Anda.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <!-- Username (Readonly) -->
        <div>
            <label class="block text-xs sm:text-sm font-bold text-stone-700 mb-1.5">
                Username / ID Pengelola
            </label>
            <div class="relative">
                <input
                    type="text"
                    value="{{ $user->username }}"
                    disabled
                    class="w-full px-3.5 py-2.5 bg-stone-100 border border-stone-200 rounded-xl text-stone-500 text-sm cursor-not-allowed font-mono"
                >
                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-stone-400">
                    Tidak dapat diubah
                </span>
            </div>
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                Nama Lengkap <span class="text-red-600">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                class="w-full px-3.5 py-2.5 bg-stone-50/50 hover:bg-white focus:bg-white border @error('name') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                Alamat Email <span class="text-red-600">*</span>
            </label>
            <input
                id="email"
                name="email"
                type="email"
                class="w-full px-3.5 py-2.5 bg-stone-50/50 hover:bg-white focus:bg-white border @error('email') border-red-500 @else border-stone-200 @enderror rounded-xl text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#7B1113]/15 focus:border-[#7B1113] transition"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="email"
            />
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center gap-4 pt-2">
            <button
                type="submit"
                class="px-5 py-2.5 bg-[#8A1315] hover:bg-[#720E10] active:bg-[#5C0B0D] text-white font-bold text-sm rounded-xl shadow-xs hover:shadow-md transition duration-150 inline-flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Perubahan</span>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs sm:text-sm text-emerald-600 font-semibold flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Profil berhasil diperbarui.</span>
                </p>
            @endif
        </div>
    </form>
</section>
