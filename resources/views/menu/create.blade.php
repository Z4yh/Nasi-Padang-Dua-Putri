<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Menu Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tambahkan hidangan masakan Padang ke dalam katalog menu
                </p>
            </div>
            <a href="{{ route('menu.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Katalog Menu
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

                <form method="POST"
                      action="{{ route('menu.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    <!-- Kategori Menu -->
                    <div>
                        <x-input-label for="id_kategori_menu" :value="__('Kategori Menu')" />
                        <span class="text-xs text-red-500 font-semibold">* Wajib</span>
                        <select id="id_kategori_menu"
                                name="id_kategori_menu"
                                class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm text-sm"
                                required>
                            <option value="">-- Pilih Kategori Menu --</option>
                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori->id_kategori_menu }}" {{ old('id_kategori_menu') == $kategori->id_kategori_menu ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('id_kategori_menu')" />
                    </div>

                    <!-- Nama Menu -->
                    <div>
                        <x-input-label for="nama_menu" :value="__('Nama Menu Masakan')" />
                        <span class="text-xs text-red-500 font-semibold">* Wajib</span>
                        <x-text-input id="nama_menu"
                                     name="nama_menu"
                                     type="text"
                                     class="mt-1 block w-full"
                                     :value="old('nama_menu')"
                                     placeholder="Contoh: Rendang Daging Sapi, Ayam Pop, Gulai Tunjang"
                                     required />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_menu')" />
                        <p class="text-xs text-gray-500 mt-1">Slug URL menu akan digenerate otomatis secara unik.</p>
                    </div>

                    <!-- Harga Menu -->
                    <div>
                        <x-input-label for="harga" :value="__('Harga (Rupiah)')" />
                        <span class="text-xs text-red-500 font-semibold">* Wajib</span>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm font-semibold">Rp</span>
                            </div>
                            <x-text-input id="harga"
                                         name="harga"
                                         type="number"
                                         min="0"
                                         step="500"
                                         class="block w-full pl-10"
                                         :value="old('harga', 0)"
                                         required />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('harga')" />
                    </div>

                    <!-- Status Stok -->
                    <div>
                        <x-input-label :value="__('Status Ketersediaan Stok')" />
                        <span class="text-xs text-red-500 font-semibold">* Wajib</span>
                        <div class="mt-2 flex items-center gap-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio"
                                       name="status_stok"
                                       value="tersedia"
                                       class="text-red-700 focus:ring-red-500"
                                       {{ old('status_stok', 'tersedia') === 'tersedia' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 font-medium">Tersedia</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio"
                                       name="status_stok"
                                       value="habis"
                                       class="text-red-700 focus:ring-red-500"
                                       {{ old('status_stok') === 'habis' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 font-medium">Habis</span>
                            </label>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('status_stok')" />
                    </div>

                    <!-- Foto Menu (W10: Upload Berkas Foto dengan Preview) -->
                    <div>
                        <x-input-label for="foto_menu" :value="__('Foto Menu (Opsional)')" />
                        <p class="text-xs text-gray-500 mb-2">
                            Format yang didukung: JPG, JPEG, PNG, WEBP. Maksimal ukuran: 2 MB (2048 KB).
                        </p>

                        <div class="flex items-start gap-4">
                            <!-- Container Live Preview -->
                            <div class="relative w-28 h-28 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                                <img id="preview-image"
                                     src=""
                                     alt="Preview Foto"
                                     class="hidden w-full h-full object-cover" />
                                <div id="preview-placeholder" class="text-center text-gray-400 p-2">
                                    <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] block mt-1">Preview</span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <input id="foto_menu"
                                       name="foto_menu"
                                       type="file"
                                       accept="image/jpeg,image/png,image/webp,image/jpg"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer"
                                       onchange="previewFotoMenu(event)" />
                                <x-input-error class="mt-2" :messages="$errors->get('foto_menu')" />
                                <button type="button"
                                        id="reset-foto-btn"
                                        class="hidden text-xs text-red-600 hover:underline mt-2 cursor-pointer"
                                        onclick="resetFotoInput()">
                                    Hapus Pilihan Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Menu -->
                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi Menu (Opsional)')" />
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm text-sm"
                                  placeholder="Deskripsi bahan, cita rasa bumbu rempah Padang, atau porsi hidangan...">{{ old('deskripsi') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('menu.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-red-700 text-white rounded-lg text-sm font-medium hover:bg-red-800 transition shadow-sm">
                            Simpan Menu
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script Live Preview Thumbnail Foto Menu -->
    <script>
        function previewFotoMenu(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('preview-image');
            const placeholder = document.getElementById('preview-placeholder');
            const resetBtn = document.getElementById('reset-foto-btn');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    resetBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function resetFotoInput() {
            const input = document.getElementById('foto_menu');
            const previewImg = document.getElementById('preview-image');
            const placeholder = document.getElementById('preview-placeholder');
            const resetBtn = document.getElementById('reset-foto-btn');

            input.value = '';
            previewImg.src = '';
            previewImg.classList.add('hidden');
            placeholder.classList.remove('hidden');
            resetBtn.classList.add('hidden');
        }
    </script>
</x-app-layout>
