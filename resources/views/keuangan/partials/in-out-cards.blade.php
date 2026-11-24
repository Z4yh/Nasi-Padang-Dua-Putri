{{-- Component: InOutCards --}}
<div class="flex flex-col gap-3.5 sm:gap-4 justify-between h-full">
    {{-- Kartu Total Masuk --}}
    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 sm:p-5 shadow-xs flex-1 flex flex-col justify-between">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Total Masuk</span>
            <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold text-xs">
                ↓
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-extrabold text-[#10996D] my-1 leading-tight tracking-tight" x-text="formatRupiah(summary.total_in)">
            Rp 0
        </div>
        <div class="border-t border-stone-100 pt-2 flex items-center justify-between text-xs text-stone-500">
            <div>
                <span>POS Kasir: </span>
                <strong class="font-bold text-stone-700" x-text="formatRingkas(summary.in_breakdown?.pos || 0)">0</strong>
            </div>
            <div>
                <span>Katering: </span>
                <strong class="font-bold text-stone-700" x-text="formatRingkas(summary.in_breakdown?.catering || 0)">0</strong>
            </div>
        </div>
    </div>

    {{-- Kartu Total Keluar --}}
    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 sm:p-5 shadow-xs flex-1 flex flex-col justify-between">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Total Keluar</span>
            <span class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-extrabold text-xs">
                ↑
            </span>
        </div>
        <div class="text-2xl sm:text-3xl font-extrabold text-[#7B1113] my-1 leading-tight tracking-tight" x-text="formatRupiah(summary.total_out)">
            Rp 0
        </div>
        <div class="border-t border-stone-100 pt-2 flex items-center justify-between text-xs text-stone-500">
            <div>
                <span>Pasar Subuh: </span>
                <strong class="font-bold text-stone-700" x-text="formatRingkas(summary.out_breakdown?.pasar_subuh || 0)">0</strong>
            </div>
            <div>
                <span>Operasional: </span>
                <strong class="font-bold text-stone-700" x-text="formatRingkas(summary.out_breakdown?.operasional || 0)">0</strong>
            </div>
        </div>
    </div>
</div>
