{{-- Component: EntryList --}}
<div class="space-y-4">
    {{-- Tabs Filter & Counter Bar (Mirip Katering) --}}
    <div class="bg-white rounded-2xl p-3 sm:p-4 border border-stone-200/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
            <button
                type="button"
                @click="setTypeFilter('all')"
                :class="typeFilter === 'all'
                    ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                    : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
                class="px-4 py-2 text-xs rounded-xl transition duration-150 flex items-center justify-center gap-2 shrink-0 select-none"
            >
                <span>Semua</span>
                <span
                    class="px-1.5 py-0.5 rounded-full text-[10px]"
                    :class="typeFilter === 'all' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700'"
                    x-text="typeFilter === 'all' ? (entriesData.total_count || 0) : '•'"
                ></span>
            </button>

            <button
                type="button"
                @click="setTypeFilter('in')"
                :class="typeFilter === 'in'
                    ? 'bg-[#10996D] text-white shadow-xs font-bold'
                    : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
                class="px-4 py-2 text-xs rounded-xl transition duration-150 flex items-center justify-center gap-2 shrink-0 select-none"
            >
                <span>Kas Masuk (+)</span>
                <span
                    class="px-1.5 py-0.5 rounded-full text-[10px]"
                    :class="typeFilter === 'in' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'"
                    x-text="typeFilter === 'in' ? (entriesData.total_count || 0) : '•'"
                ></span>
            </button>

            <button
                type="button"
                @click="setTypeFilter('out')"
                :class="typeFilter === 'out'
                    ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                    : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
                class="px-4 py-2 text-xs rounded-xl transition duration-150 flex items-center justify-center gap-2 shrink-0 select-none"
            >
                <span>Kas Keluar (–)</span>
                <span
                    class="px-1.5 py-0.5 rounded-full text-[10px]"
                    :class="typeFilter === 'out' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800'"
                    x-text="typeFilter === 'out' ? (entriesData.total_count || 0) : '•'"
                ></span>
            </button>
        </div>

        <div class="text-xs text-stone-500 font-medium text-right sm:text-left">
            <span>Riwayat Arus Kas <strong class="text-stone-700" x-text="periodLabelShort"></strong>: </span>
            <strong class="text-[#7B1113]" x-text="(entriesData.total_count || 0) + ' Transaksi'"></strong>
        </div>
    </div>

    {{-- Grid Kartu Riwayat Transaksi (Desktop: 3 Kolom, Tablet: 2 Kolom, Mobile: 1 Kolom) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
        <template x-for="item in entriesData.items" :key="item.id">
            <div
                @click="item.editable ? onEditExpense(item) : null"
                :class="item.editable
                    ? 'cursor-pointer hover:border-[#7B1113]/50 hover:shadow-md bg-white'
                    : 'bg-white shadow-xs'"
                class="rounded-2xl border border-stone-200/80 p-4 transition flex flex-col justify-between space-y-3 relative group"
                :title="item.editable ? 'Klik untuk mengubah atau menghapus pengeluaran ini' : ''"
            >
                {{-- Baris Atas: Judul (Wrap hingga 2 baris, tidak truncate) & Nominal --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-1.5 flex-1 min-w-0">
                        <template x-if="item.editable">
                            <span class="text-stone-400 group-hover:text-[#7B1113] transition shrink-0" title="Dapat diedit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </span>
                        </template>
                        <h6 class="font-bold text-stone-800 text-sm leading-snug line-clamp-2 break-words" x-text="item.title"></h6>
                    </div>
                    <div
                        class="text-sm sm:text-base font-extrabold shrink-0 tracking-tight"
                        :class="item.type === 'in' ? 'text-[#10996D]' : 'text-[#7B1113]'"
                        x-text="(item.type === 'in' ? '+' : '-') + formatRupiah(item.amount)"
                    ></div>
                </div>

                {{-- Baris Bawah: Subtitle & Badge --}}
                <div class="border-t border-stone-100 pt-2.5 flex items-center justify-between text-xs">
                    <div class="text-stone-500 truncate mr-2 flex items-center gap-1.5 text-[11px]">
                        <span class="truncate" x-text="item.subtitle"></span>
                        <span class="text-stone-300 shrink-0">•</span>
                        <span class="shrink-0 font-medium" x-text="formatTime(item.occurred_at)"></span>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <template x-if="item.editable">
                            <span class="text-[10px] text-[#7B1113] font-semibold opacity-0 group-hover:opacity-100 transition hidden sm:inline">
                                Ubah &rsaquo;
                            </span>
                        </template>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 uppercase tracking-wide"
                            :class="item.type === 'in'
                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                : 'bg-rose-50 text-rose-700 border border-rose-200'"
                            x-text="item.type === 'in' ? 'Masuk' : 'Keluar'"
                        ></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <template x-if="!loadingEntries && (!entriesData.items || entriesData.items.length === 0)">
        <div class="bg-white rounded-2xl border border-dashed border-stone-300/80 p-10 text-center space-y-3 shadow-2xs">
            <div class="w-12 h-12 mx-auto rounded-full bg-[#FAF6F0] border border-[#F0E6D6] flex items-center justify-center text-[#9A6B1F]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-base font-bold text-stone-800">Belum ada transaksi pada periode ini</p>
                <p class="text-xs text-stone-500 mt-1">Transaksi dari Kasir POS, Pesanan Katering, atau Pengeluaran tercatat di sini.</p>
            </div>
        </div>
    </template>

    {{-- Loading More Indicator --}}
    <template x-if="loadingMore">
        <div class="text-center py-3 text-xs text-stone-400 font-medium animate-pulse">
            Memuat transaksi berikutnya...
        </div>
    </template>

    {{-- Tombol Muat Lebih Banyak (Pagination) --}}
    <template x-if="!loadingMore && entriesData.next_cursor">
        <div class="pt-2 text-center">
            <button
                type="button"
                @click="loadMoreEntries()"
                class="py-2.5 px-6 bg-white hover:bg-stone-50 border border-stone-300 rounded-xl text-xs sm:text-sm font-bold text-stone-700 transition shadow-xs active:scale-95"
            >
                Muat Lebih Banyak
            </button>
        </div>
    </template>
</div>
