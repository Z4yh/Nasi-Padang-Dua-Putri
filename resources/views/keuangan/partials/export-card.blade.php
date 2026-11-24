{{-- Component: ExportCard --}}
<div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h5 class="font-bold text-stone-800 text-sm sm:text-base">
            Ekspor Pembukuan
        </h5>
        <p class="text-xs text-stone-500 mt-0.5">
            Cetak berkas audit internal pemilik RM Padang Tiga Putri.
        </p>
    </div>

    <div class="flex items-center gap-2.5 w-full sm:w-auto">
        {{-- Tombol Unduh PDF (Outline) --}}
        <button
            type="button"
            @click="onExport('pdf')"
            class="flex-1 sm:flex-none py-2.5 px-4 bg-white hover:bg-stone-50 border border-stone-300 rounded-xl font-bold text-xs text-stone-700 transition flex items-center justify-center gap-2 shadow-2xs active:scale-95"
            title="Ekspor berkas pembukuan format PDF"
        >
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span>Unduh PDF</span>
        </button>

        {{-- Tombol Unduh Excel CSV (Solid Merah Marun) --}}
        <button
            type="button"
            @click="onExport('csv')"
            class="flex-1 sm:flex-none py-2.5 px-5 bg-[#7B1113] hover:bg-[#630E10] active:bg-[#4E0A0C] text-white rounded-xl font-bold text-xs transition flex items-center justify-center gap-2 shadow-xs active:scale-95"
            title="Ekspor rekap spreadsheet Excel/CSV"
        >
            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Excel CSV</span>
        </button>
    </div>
</div>
