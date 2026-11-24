{{-- Component: SummaryCard --}}
<div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs relative overflow-hidden p-5 flex flex-col justify-between transition-all">
    {{-- Aksen Gradasi Emas -> Merah di Tepi Atas --}}
    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#DDA73A] via-[#B8383B] to-[#7B1113]"></div>

    <div>
        {{-- Baris Label & Badge Pertumbuhan --}}
        <div class="flex items-center justify-between gap-2 pt-1 mb-2">
            <span class="text-xs font-extrabold tracking-wider text-stone-500 uppercase">
                LABA BERSIH USAHA
            </span>

            {{-- Badge Pertumbuhan --}}
            <div>
                <template x-if="summary.growth_pct !== null && summary.growth_pct > 0">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span x-text="'+' + formatNumber(summary.growth_pct) + '% ' + (summary.compare_label || '')"></span>
                    </span>
                </template>

                <template x-if="summary.growth_pct !== null && summary.growth_pct < 0">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                        <span x-text="formatNumber(summary.growth_pct) + '% ' + (summary.compare_label || '')"></span>
                    </span>
                </template>

                <template x-if="summary.growth_pct === null || summary.growth_pct === 0">
                    <span class="text-stone-400 font-bold text-xs">—</span>
                </template>
            </div>
        </div>

        {{-- Nominal Laba Bersih Besar --}}
        <div class="mt-1 mb-2">
            <h3
                class="text-3xl sm:text-4xl font-extrabold font-serif-title tracking-tight leading-none"
                :class="summary.net_profit < 0 ? 'text-rose-600' : 'text-[#7B1113]'"
                x-text="formatRupiah(summary.net_profit)"
            ></h3>
        </div>

        {{-- Margin Laba & Rumus --}}
        <div class="flex items-center justify-between text-xs text-stone-600 mt-2">
            <div>
                <span>Margin Laba: </span>
                <span class="font-bold text-stone-800" x-text="(summary.margin_pct !== undefined ? formatNumber(summary.margin_pct) : '0') + '%'"></span>
            </div>
            <div class="italic text-stone-600">
                Rumus: Masuk – Keluar
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-t border-stone-100">
        {{-- Progress Bar Dua Segmen (Kas Masuk vs Kas Keluar) --}}
        <div class="h-2.5 rounded-full overflow-hidden flex bg-stone-100 border border-stone-200/60">
            <div
                class="bg-[#10996D] h-full transition-all duration-500"
                :style="'width: ' + (summary.in_pct || 0) + '%'"
            ></div>
            <div
                class="bg-[#E5484D] h-full transition-all duration-500"
                :style="'width: ' + (summary.out_pct || 0) + '%'"
            ></div>
        </div>

        {{-- Legenda Titik Warna --}}
        <div class="flex items-center justify-between text-xs text-stone-600 mt-2 font-medium">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#10996D] inline-block"></span>
                <span x-text="'Kas Masuk (' + (summary.in_pct !== undefined ? formatNumber(summary.in_pct) : '0') + '%)'"></span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#E5484D] inline-block"></span>
                <span x-text="'Kas Keluar (' + (summary.out_pct !== undefined ? formatNumber(summary.out_pct) : '0') + '%)'"></span>
            </div>
        </div>
    </div>
</div>
