<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Kategori Menu') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ubah informasi kategori: <span class="font-semibold text-red-700">{{ $kategoriMenu->nama_kategori }}</span>
                </p>
            </div>
            <a href="{{ route('kategori-menu.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

                <form method="POST" action="{{ route('kategori-menu.update', $kategoriMenu) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Kategori -->
                    <div>
                        <x-input-label for="nama_kategori" :value="__('Nama Kategori')" />
                        <span class="text-xs text-red-500 font-semibold">* Wajib</span>
                        <x-text-input id="nama_kategori"
                                     name="nama_kategori"
                                     type="text"
                                     class="mt-1 block w-full"
                                     :value="old('nama_kategori', $kategoriMenu->nama_kategori)"
                                     required
                                     autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_kategori')" />
                    </div>

                    <!-- Slug Saat Ini (Info Read-only) -->
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Slug URL Saat Ini</span>
                        <code class="text-xs font-mono text-gray-800">{{ $kategoriMenu->slug_kategori }}</code>
                        <p class="text-xs text-gray-500 mt-1">Slug akan diperbarui secara otomatis jika nama kategori diubah.</p>
                    </div>

                    <!-- Urutan -->
                    <div>
                        <x-input-label for="urutan" :value="__('Urutan Tampilan')" />
                        <x-text-input id="urutan"
                                     name="urutan"
                                     type="number"
                                     min="0"
                                     class="mt-1 block w-full"
                                     :value="old('urutan', $kategoriMenu->urutan)" />
                        <x-input-error class="mt-2" :messages="$errors->get('urutan')" />
                        <p class="text-xs text-gray-500 mt-1">
                            Nomor urutan menentukan posisi kategori pada menu (angka terkecil tampil paling atas).
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('kategori-menu.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-red-700 text-white rounded-lg text-sm font-medium hover:bg-red-800 transition shadow-sm">
                            Perbarui Kategori
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
