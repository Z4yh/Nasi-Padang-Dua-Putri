<section class="space-y-4">
    <header class="flex items-center gap-3 pb-3 border-b border-red-50">
        <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-stone-900">
                Hapus Akun Pengelola
            </h2>
            <p class="text-xs sm:text-sm text-stone-500">
                Tindakan ini permanen. Seluruh hak akses dan kredensial akun Anda akan dihapus dari sistem.
            </p>
        </div>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition inline-flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Hapus Akun Saya</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-stone-900">
                Apakah Anda yakin ingin menghapus akun ini?
            </h2>

            <p class="mt-2 text-sm text-stone-500">
                Setelah akun dihapus, Anda tidak akan dapat masuk kembali ke sistem. Masukkan kata sandi akun Anda untuk mengonfirmasi penghapusan permanen.
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">Kata Sandi</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full sm:w-3/4 px-3.5 py-2.5 border border-stone-200 rounded-xl text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition"
                    placeholder="Masukkan kata sandi untuk konfirmasi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 border border-stone-200 hover:bg-stone-50 rounded-xl text-xs sm:text-sm font-semibold text-stone-700 transition"
                >
                    Batalkan
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition"
                >
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
