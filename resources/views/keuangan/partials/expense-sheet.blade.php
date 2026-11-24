{{-- Component: ExpenseSheet (Bottom Sheet Tambah & Ubah Pengeluaran) --}}
<div
    x-cloak
    @keydown.escape.window="handleEscapeKey()"
>
    {{-- Container Fixed Viewport (Mendukung Bottom Sheet di Mobile & Dialog Terpusat Sempurna di Desktop) --}}
    <div
        x-show="sheetOpen"
        class="fixed inset-0 z-[100] overflow-y-auto flex items-end sm:items-center justify-center p-0 sm:p-4"
    >
        {{-- 1. Backdrop Overlay --}}
        <div
            x-show="sheetOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
            @click="requestCloseSheet()"
            aria-hidden="true"
        ></div>

        {{-- 2. Sheet / Modal Panel (Tidak Akan Terpotong di Atas Maupun Bawah) --}}
        <div
            x-show="sheetOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
            x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
            x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
            class="relative w-full max-w-lg bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl p-5 sm:p-6 overflow-y-auto max-h-[90vh] border border-stone-200/80 z-10 mx-auto my-0 sm:my-auto"
            role="dialog"
            aria-modal="true"
            :aria-label="isEditMode ? 'Ubah Pengeluaran' : 'Catat Pengeluaran'"
        >
        {{-- Drag Handle (Mobile Ergonomics) --}}
        <div class="w-12 h-1.5 bg-stone-300 rounded-full mx-auto mb-3 cursor-grab select-none"></div>

        {{-- Tombol Tutup ✕ --}}
        <button
            type="button"
            @click="requestCloseSheet()"
            class="absolute top-4 right-4 p-2 text-stone-400 hover:text-stone-700 hover:bg-stone-100 rounded-full transition"
            aria-label="Tutup Formulir"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Header: Judul Serif Kapital & Aksen Garis Emas-Merah --}}
        <div class="text-center pt-1 pb-4">
            <h3
                class="font-serif-title text-xl sm:text-2xl font-black text-[#7B1113] tracking-wider uppercase"
                x-text="isEditMode ? 'UBAH PENGELUARAN' : 'CATAT PENGELUARAN'"
            ></h3>
            <div class="h-0.5 w-36 mx-auto bg-gradient-to-r from-amber-400 via-[#7B1113] to-amber-400 mt-1.5"></div>
        </div>

        {{-- Pesan Error Global Server --}}
        <template x-if="formErrors.general">
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="formErrors.general"></span>
            </div>
        </template>

        {{-- Form Isian --}}
        <form @submit.prevent="saveExpense()" class="space-y-4 sm:space-y-5">

            {{-- 1. KATEGORI PENGELUARAN --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs sm:text-sm font-bold text-stone-800">
                        Kategori Pengeluaran
                    </label>
                    <span class="text-[11px] font-semibold text-[#9A6B1F]">
                        *Pilih salah satu
                    </span>
                </div>

                {{-- Chips Single-Select --}}
                <div class="flex flex-wrap gap-2">
                    <template x-for="cat in categoryOptions" :key="cat.value">
                        <button
                            type="button"
                            @click="selectCategory(cat.value)"
                            :class="formData.category === cat.value
                                ? 'bg-[#7B1113] text-white shadow-xs font-bold border-transparent'
                                : 'bg-[#FAF6F0] text-stone-700 hover:bg-[#F2ECE1] border-stone-200/80 font-medium'"
                            class="px-3.5 py-2 rounded-xl text-xs border transition duration-150 active:scale-95 select-none"
                            x-text="cat.label"
                        ></button>
                    </template>
                </div>
                <template x-if="formErrors.category">
                    <p class="text-[11px] text-rose-600 font-medium mt-1.5" x-text="formErrors.category"></p>
                </template>
            </div>

            {{-- 2. JUMLAH PENGELUARAN (NOMINAL) --}}
            <div>
                <label for="expense_amount" class="block text-xs sm:text-sm font-bold text-stone-800 mb-1.5">
                    Jumlah Pengeluaran (Nominal)
                </label>
                <div
                    class="flex items-center bg-[#FAF6F0] border rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-[#7B1113]/30 focus-within:border-[#7B1113] transition"
                    :class="formErrors.amount ? 'border-rose-300' : 'border-stone-200'"
                >
                    <span class="pl-4 pr-1 text-sm sm:text-base font-bold text-stone-700 select-none">
                        Rp
                    </span>
                    <input
                        id="expense_amount"
                        type="text"
                        inputmode="numeric"
                        x-model="formAmountDisplay"
                        @input="onAmountInput($event)"
                        placeholder="contoh: 350000"
                        class="w-full bg-transparent border-0 px-2 py-3 text-sm sm:text-base font-semibold text-stone-900 placeholder:text-stone-400 focus:ring-0"
                        required
                    />
                </div>
                <template x-if="formErrors.amount">
                    <p class="text-[11px] text-rose-600 font-medium mt-1.5" x-text="formErrors.amount"></p>
                </template>
            </div>

            {{-- 3. KETERANGAN / RINCIAN BELANJA --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="expense_note" class="block text-xs sm:text-sm font-bold text-stone-800">
                        Keterangan / Rincian Belanja
                    </label>
                    <span
                        class="text-[11px]"
                        :class="formData.category === 'lain_lain' ? 'text-rose-600 font-bold' : 'text-stone-400'"
                        x-text="formData.category === 'lain_lain' ? '*Wajib untuk Lain-lain' : 'Opsional'"
                    ></span>
                </div>
                <input
                    id="expense_note"
                    type="text"
                    maxlength="200"
                    x-model="formData.note"
                    @input="clearError('note')"
                    placeholder="misal: Daging paha 4kg & kelapa parut"
                    class="w-full bg-[#FAF6F0] border rounded-xl px-4 py-3 text-xs sm:text-sm text-stone-900 placeholder:text-stone-400 focus:ring-2 focus:ring-[#7B1113]/30 focus:border-[#7B1113] transition"
                    :class="formErrors.note ? 'border-rose-300' : 'border-stone-200'"
                />
                <template x-if="formErrors.note">
                    <p class="text-[11px] text-rose-600 font-medium mt-1.5" x-text="formErrors.note"></p>
                </template>
            </div>

            {{-- 4. SUMBER DANA / PEMBAYARAN --}}
            <div>
                <label class="block text-xs sm:text-sm font-bold text-stone-800 mb-2">
                    Sumber Dana / Pembayaran
                </label>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Opsi 1: Kas Tunai Warung --}}
                    <div
                        @click="selectFundSource('kas_tunai')"
                        :class="formData.fund_source === 'kas_tunai'
                            ? 'border-[#7B1113] bg-[#FDF4F4] shadow-2xs'
                            : 'border-stone-200 bg-white hover:bg-stone-50'"
                        class="border rounded-2xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none"
                    >
                        <div class="pt-0.5">
                            <div
                                :class="formData.fund_source === 'kas_tunai'
                                    ? 'border-[#7B1113] bg-white'
                                    : 'border-stone-300 bg-white'"
                                class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0"
                            >
                                <div
                                    x-show="formData.fund_source === 'kas_tunai'"
                                    class="w-2 h-2 rounded-full bg-[#7B1113]"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-stone-900 leading-tight">
                                Kas Tunai Warung
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">
                                Dari Laci Kasir POS
                            </div>
                        </div>
                    </div>

                    {{-- Opsi 2: Transfer Bank --}}
                    <div
                        @click="selectFundSource('transfer_bank')"
                        :class="formData.fund_source === 'transfer_bank'
                            ? 'border-[#7B1113] bg-[#FDF4F4] shadow-2xs'
                            : 'border-stone-200 bg-white hover:bg-stone-50'"
                        class="border rounded-2xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none"
                    >
                        <div class="pt-0.5">
                            <div
                                :class="formData.fund_source === 'transfer_bank'
                                    ? 'border-[#7B1113] bg-white'
                                    : 'border-stone-300 bg-white'"
                                class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0"
                            >
                                <div
                                    x-show="formData.fund_source === 'transfer_bank'"
                                    class="w-2 h-2 rounded-full bg-[#7B1113]"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-stone-900 leading-tight">
                                Transfer Bank
                            </div>
                            <div class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">
                                Rekening Operasional
                            </div>
                        </div>
                    </div>
                </div>
                <template x-if="formErrors.fund_source">
                    <p class="text-[11px] text-rose-600 font-medium mt-1.5" x-text="formErrors.fund_source"></p>
                </template>
            </div>

            {{-- 5. TANGGAL & WAKTU (OPSIONAL / RINGKAS) --}}
            <div class="pt-1">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="expense_occurred_at" class="block text-xs font-semibold text-stone-600">
                        Waktu Belanja (Default: Sekarang)
                    </label>
                    <span class="text-[10px] text-stone-400">Tidak boleh masa depan</span>
                </div>
                <input
                    id="expense_occurred_at"
                    type="datetime-local"
                    x-model="formData.occurred_at"
                    :max="maxDateTimeString"
                    class="w-full bg-[#FAF6F0] border border-stone-200 rounded-xl px-3 py-2 text-xs text-stone-800 focus:ring-2 focus:ring-[#7B1113]/30 focus:border-[#7B1113] transition"
                />
                <template x-if="formErrors.occurred_at">
                    <p class="text-[11px] text-rose-600 font-medium mt-1.5" x-text="formErrors.occurred_at"></p>
                </template>
            </div>

            {{-- 6. TOMBOL AKSI UTAMA & SEKUNDER --}}
            <div class="pt-3 space-y-2.5">
                <button
                    type="submit"
                    :disabled="isSubmitting || !isFormValid"
                    class="w-full py-3.5 px-4 bg-[#7B1113] hover:bg-[#630E10] active:bg-[#4E0A0C] text-white rounded-xl font-bold text-sm transition shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-98"
                >
                    <template x-if="isSubmitting">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Simpan')"></span>
                </button>

                {{-- Tombol Hapus (Hanya muncul saat mode Ubah) --}}
                <template x-if="isEditMode">
                    <button
                        type="button"
                        @click="promptDeleteExpense()"
                        :disabled="isSubmitting"
                        class="w-full py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl font-bold text-xs transition flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus Pengeluaran</span>
                    </button>
                </template>
            </div>

        </form>
        </div>
    </div>

    {{-- 3. Modal Konfirmasi Buang Perubahan (Discard Changes) --}}
    <div
        x-show="showDiscardModal"
        x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm"
    >
        <div
            @click.away="showDiscardModal = false"
            class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-stone-200 text-center space-y-4"
        >
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-stone-900 text-base">Buang Perubahan?</h4>
                <p class="text-xs text-stone-500 mt-1">
                    Isian formulir pengeluaran yang belum disimpan akan hilang.
                </p>
            </div>
            <div class="flex items-center gap-3 pt-1">
                <button
                    type="button"
                    @click="showDiscardModal = false"
                    class="flex-1 py-2.5 px-3 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold rounded-xl transition"
                >
                    Lanjut Mengisi
                </button>
                <button
                    type="button"
                    @click="confirmDiscardChanges()"
                    class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-xs"
                >
                    Buang Perubahan
                </button>
            </div>
        </div>
    </div>

    {{-- 4. Modal Konfirmasi Hapus Pengeluaran --}}
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm"
    >
        <div
            @click.away="showDeleteModal = false"
            class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-stone-200 text-center space-y-4"
        >
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-700 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-stone-900 text-base">Hapus Pengeluaran Ini?</h4>
                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                    Tindakan ini akan menghapus catatan dan memperbarui laba serta ringkasan pada periode terkait.
                </p>
            </div>
            <div class="flex items-center gap-3 pt-1">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    :disabled="isDeleting"
                    class="flex-1 py-2.5 px-3 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold rounded-xl transition disabled:opacity-50"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="executeDeleteExpense()"
                    :disabled="isDeleting"
                    class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center justify-center gap-1.5 disabled:opacity-50"
                >
                    <template x-if="isDeleting">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
