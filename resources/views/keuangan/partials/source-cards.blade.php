{{-- Component: SourceCards --}}
<div class="bg-white rounded-2xl border border-stone-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between h-full space-y-3.5">
    <div>
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-extrabold tracking-wider uppercase text-stone-500">Sumber Pemasukan</span>
            <span class="text-xs font-bold text-stone-700">
                Total: <span x-text="formatRupiah(summary.total_in)"></span>
            </span>
        </div>

        {{-- Sub-Card 1: POS Kasir --}}
        <div class="p-3 bg-[#FAF6F0] rounded-xl border border-[#F0E6D6] space-y-1.5 mb-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="font-bold text-stone-800 text-xs sm:text-sm">Kasir POS (Warung Fisik)</h6>
                    <p class="text-[11px] text-stone-500">
                        <span x-text="(summary.sources?.pos?.tx_count || 0) + ' Transaksi'"></span>
                        <span>•</span>
                        <span x-text="(summary.sources?.pos?.cash_count || 0) + ' Tunai, ' + (summary.sources?.pos?.qris_count || 0) + ' QRIS'"></span>
                    </p>
                </div>
                <div class="font-extrabold text-sm sm:text-base text-[#9A6B1F]" x-text="formatRupiah(summary.sources?.pos?.total || 0)">
                    Rp 0
                </div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-stone-600 border-t border-[#EAE0D0] pt-1">
                <span>Tunai: <strong class="font-bold text-stone-800" x-text="formatRupiah(summary.sources?.pos?.cash_total || 0)"></strong></span>
                <span>QRIS: <strong class="font-bold text-stone-800" x-text="formatRupiah(summary.sources?.pos?.qris_total || 0)"></strong></span>
            </div>
        </div>

        {{-- Sub-Card 2: Pesanan Katering --}}
        <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100/90 space-y-1.5">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="font-bold text-stone-800 text-xs sm:text-sm">Pesanan Katering</h6>
                    <p class="text-[11px] text-stone-500" x-text="(summary.sources?.catering?.order_count || 0) + ' Pesanan Selesai / Lunas ' + periodLabelShort"></p>
                </div>
                <div class="font-extrabold text-sm sm:text-base text-[#9A6B1F]" x-text="formatRupiah(summary.sources?.catering?.total || 0)">
                    Rp 0
                </div>
            </div>

            {{-- List Rincian Katering Top 3 --}}
            <div class="space-y-1 pt-1 border-t border-emerald-100">
                <template x-for="item in (summary.sources?.catering?.items || [])" :key="item.code">
                    <a
                        href="{{ route('katering.index') }}"
                        class="flex items-center justify-between text-[11px] bg-white/80 hover:bg-white px-2 py-1 rounded-md border border-emerald-100/80 transition"
                    >
                        <div class="truncate text-emerald-900 font-medium">
                            <span class="font-bold" x-text="'#' + item.code"></span>
                            <span x-text="' ' + item.title"></span>
                        </div>
                        <div class="shrink-0 font-bold text-emerald-700 ml-2">
                            <span x-text="formatRupiah(item.amount)"></span>
                            <span class="text-[10px] text-emerald-600 font-semibold" x-text="'(' + (item.status === 'dp' ? ('DP ' + item.dp_pct + '%') : 'Lunas') + ')'"></span>
                        </div>
                    </a>
                </template>

                <template x-if="!summary.sources?.catering?.items || summary.sources.catering.items.length === 0">
                    <div class="text-[11px] text-stone-400 italic text-center py-0.5">
                        Tidak ada transaksi katering.
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Link Lihat Semua Katering --}}
    <template x-if="(summary.sources?.catering?.order_count || 0) > 3">
        <div class="text-right pt-1">
            <a href="{{ route('katering.index') }}" class="text-xs font-semibold text-[#7B1113] hover:underline">
                Lihat semua (<span x-text="summary.sources.catering.order_count"></span> pesanan) →
            </a>
        </div>
    </template>
</div>
