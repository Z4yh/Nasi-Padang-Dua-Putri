{{-- Component: PeriodFilter --}}
<div class="bg-white rounded-2xl border border-stone-200/80 p-3 sm:p-4 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
    {{-- Left: Chip Pill Filter --}}
    <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar">
        <button
            type="button"
            @click="setPeriod('day')"
            :class="period === 'day'
                ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
            class="px-4 py-2 text-xs rounded-xl transition duration-150 text-center select-none shrink-0"
        >
            Hari Ini
        </button>

        <button
            type="button"
            @click="setPeriod('week')"
            :class="period === 'week'
                ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
            class="px-4 py-2 text-xs rounded-xl transition duration-150 text-center select-none shrink-0"
        >
            Minggu Ini
        </button>

        <button
            type="button"
            @click="setPeriod('month')"
            :class="period === 'month'
                ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
            class="px-4 py-2 text-xs rounded-xl transition duration-150 text-center select-none shrink-0"
        >
            Bulan Ini
        </button>

        <button
            type="button"
            @click="setPeriod('year')"
            :class="period === 'year'
                ? 'bg-[#7B1113] text-white shadow-xs font-bold'
                : 'bg-[#FAF6F0] hover:bg-[#F3EBE0] text-stone-700 font-semibold border border-stone-200/60'"
            class="px-4 py-2 text-xs rounded-xl transition duration-150 text-center select-none shrink-0"
        >
            Tahun Ini
        </button>
    </div>

    {{-- Right: Tanggal Label Dinamis --}}
    <div class="flex items-center justify-between sm:justify-end gap-2 text-xs sm:text-sm pt-2 sm:pt-0 border-t sm:border-t-0 border-stone-100">
        <span class="font-medium text-stone-500">Periode Pembukuan:</span>
        <span class="font-bold text-[#7B1113]" x-text="summary.label || 'Memuat...'"></span>
    </div>
</div>
