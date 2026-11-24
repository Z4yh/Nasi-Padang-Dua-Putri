<x-app-layout>
    {{-- Google Fonts: Playfair Display untuk judul Laporan Arus Kas --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">

    <style>
        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .shadow-2xs {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div
        x-data="keuanganApp('{{ $initialPeriod ?? 'day' }}', '{{ $initialDate ?? '' }}')"
        x-init="init()"
        class="py-6 sm:py-8 bg-[#FAF6F0] min-h-screen text-stone-800"
    >
        {{-- Container Luas Menyesuaikan Katering (max-w-7xl) --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ============================================================= --}}
            {{-- 1. HEADER HALAMAN: JUDUL, SUBTITLE, DAN TOMBOL AKSI           --}}
            {{-- ============================================================= --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold font-serif-title text-[#7B1113] leading-tight">
                        Laporan Arus Kas
                    </h2>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Rekapitulasi otomatis dari Kasir POS &amp; Pesanan Katering.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <button
                        type="button"
                        @click="onExport('pdf')"
                        class="inline-flex items-center px-3.5 py-2 bg-white border border-stone-300 rounded-xl font-bold text-xs text-stone-700 hover:bg-stone-50 transition shadow-2xs active:scale-95"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Unduh PDF
                    </button>

                    <button
                        type="button"
                        @click="onExport('csv')"
                        class="inline-flex items-center px-3.5 py-2 bg-white border border-stone-300 rounded-xl font-bold text-xs text-stone-700 hover:bg-stone-50 transition shadow-2xs active:scale-95"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh CSV
                    </button>

                    <button
                        type="button"
                        @click="onAddExpense()"
                        class="inline-flex items-center px-4 py-2 bg-[#7B1113] hover:bg-[#630E10] active:bg-[#4E0A0C] border border-transparent rounded-xl font-bold text-xs sm:text-sm text-white transition shadow-sm active:scale-95 ml-auto sm:ml-0"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Pengeluaran</span>
                    </button>
                </div>
            </div>

            {{-- Ornamen Pemisah Minangkabau --}}
            <div class="flex items-center my-1">
                <div class="flex-1 border-t border-[#EAE3D9]"></div>
                <div class="px-3 flex items-center gap-1.5 text-[#C59B27] select-none text-xs">
                    <span>◆</span>
                    <span class="text-sm">❖</span>
                    <span>◆</span>
                </div>
                <div class="flex-1 border-t border-[#EAE3D9]"></div>
            </div>

            {{-- ============================================================= --}}
            {{-- 2. NOTIFIKASI ERROR (Bila Terjadi Masalah API)                 --}}
            {{-- ============================================================= --}}
            <template x-if="errorMessage">
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-xs flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="errorMessage"></span>
                    </div>
                    <button
                        type="button"
                        @click="loadAll()"
                        class="px-3 py-1 bg-rose-700 text-white font-bold text-xs rounded-lg hover:bg-rose-800 transition"
                    >
                        Coba Lagi
                    </button>
                </div>
            </template>

            {{-- ============================================================= --}}
            {{-- 3. FILTER PERIODE PEMBUKUAN                                   --}}
            {{-- ============================================================= --}}
            @include('keuangan.partials.period-filter')

            {{-- ============================================================= --}}
            {{-- SKELETON LOADER UNTUK KARTU UTAMA                             --}}
            {{-- ============================================================= --}}
            <template x-if="loadingSummary">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 animate-pulse">
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 h-56"></div>
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 h-56"></div>
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 h-56"></div>
                </div>
            </template>

            {{-- ============================================================= --}}
            {{-- 4. KARTU STATISTIK RINGKASAN ATAS (3 Kolom Grid Mirip Katering)--}}
            {{-- ============================================================= --}}
            <div x-show="!loadingSummary" class="grid grid-cols-1 lg:grid-cols-3 gap-5" style="display: none;">
                {{-- Kolom 1: Kartu Laba Bersih Usaha (Section 3.4) --}}
                @include('keuangan.partials.summary-card')

                {{-- Kolom 2: Ringkasan Masuk / Keluar (Section 3.5) --}}
                @include('keuangan.partials.in-out-cards')

                {{-- Kolom 3: Sumber Pemasukan POS & Katering (Section 3.6) --}}
                @include('keuangan.partials.source-cards')
            </div>

            {{-- ============================================================= --}}
            {{-- 5. RIWAYAT ARUS KAS (Section 3.7)                             --}}
            {{-- ============================================================= --}}
            <div class="pt-2">
                @include('keuangan.partials.entry-list')
            </div>

            {{-- ============================================================= --}}
            {{-- 6. KARTU EKSPOR PEMBUKUAN (Section 3.8)                       --}}
            {{-- ============================================================= --}}
            <div class="pt-2">
                @include('keuangan.partials.export-card')
            </div>

        </div>
    </div>

    {{-- Script Alpine.js Logika Interaktif Client --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('keuanganApp', (initialPeriod = 'day', initialDate = '') => ({
                period: initialPeriod || 'day',
                date: initialDate || '',
                typeFilter: 'all',

                // Data State
                summary: {
                    period: 'day',
                    label: '',
                    net_profit: 0,
                    margin_pct: 0,
                    growth_pct: null,
                    compare_label: 'vs kemarin',
                    total_in: 0,
                    total_out: 0,
                    in_pct: 0,
                    out_pct: 0,
                    in_breakdown: { pos: 0, catering: 0 },
                    out_breakdown: { pasar_subuh: 0, operasional: 0 },
                    sources: {
                        pos: { total: 0, tx_count: 0, cash_count: 0, qris_count: 0, cash_total: 0, qris_total: 0 },
                        catering: { total: 0, order_count: 0, items: [] }
                    }
                },
                entriesData: {
                    items: [],
                    total_count: 0,
                    next_cursor: null
                },

                // Loading States
                loadingSummary: true,
                loadingEntries: true,
                loadingMore: false,
                errorMessage: null,

                // AbortControllers untuk mencegah request race condition
                summaryAbort: null,
                entriesAbort: null,

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('period')) {
                        this.period = urlParams.get('period');
                    }
                    if (urlParams.has('date')) {
                        this.date = urlParams.get('date');
                    }

                    this.loadAll();
                },

                get periodLabelShort() {
                    switch (this.period) {
                        case 'week': return 'Minggu Ini';
                        case 'month': return 'Bulan Ini';
                        case 'year': return 'Tahun Ini';
                        default: return 'Hari Ini';
                    }
                },

                setPeriod(newPeriod) {
                    if (this.period === newPeriod) return;
                    this.period = newPeriod;

                    const url = new URL(window.location);
                    url.searchParams.set('period', this.period);
                    if (this.date) {
                        url.searchParams.set('date', this.date);
                    }
                    window.history.replaceState({}, '', url);

                    this.loadAll();
                },

                setTypeFilter(type) {
                    if (this.typeFilter === type) return;
                    this.typeFilter = type;
                    this.loadEntries(true);
                },

                loadAll() {
                    this.errorMessage = null;
                    this.loadSummary();
                    this.loadEntries(true);
                },

                async loadSummary() {
                    this.loadingSummary = true;
                    if (this.summaryAbort) {
                        this.summaryAbort.abort();
                    }
                    this.summaryAbort = new AbortController();

                    try {
                        const params = new URLSearchParams({ period: this.period });
                        if (this.date) params.append('date', this.date);

                        const res = await fetch(`/api/finance/summary?${params.toString()}`, {
                            signal: this.summaryAbort.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) {
                            throw new Error('Gagal memuat ringkasan arus kas');
                        }

                        this.summary = await res.json();
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            console.error(e);
                            this.errorMessage = 'Terjadi kesalahan saat memuat data ringkasan keuangan.';
                        }
                    } finally {
                        this.loadingSummary = false;
                    }
                },

                async loadEntries(reset = true) {
                    if (reset) {
                        this.loadingEntries = true;
                    } else {
                        this.loadingMore = true;
                    }

                    if (this.entriesAbort) {
                        this.entriesAbort.abort();
                    }
                    this.entriesAbort = new AbortController();

                    try {
                        const params = new URLSearchParams({
                            period: this.period,
                            type: this.typeFilter,
                            limit: 20
                        });
                        if (this.date) params.append('date', this.date);
                        if (!reset && this.entriesData.next_cursor) {
                            params.append('cursor', this.entriesData.next_cursor);
                        }

                        const res = await fetch(`/api/finance/entries?${params.toString()}`, {
                            signal: this.entriesAbort.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) {
                            throw new Error('Gagal memuat riwayat arus kas');
                        }

                        const data = await res.json();

                        if (reset) {
                            this.entriesData = data;
                        } else {
                            this.entriesData.items = [...this.entriesData.items, ...data.items];
                            this.entriesData.next_cursor = data.next_cursor;
                            this.entriesData.total_count = data.total_count;
                        }
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            console.error(e);
                            this.errorMessage = 'Terjadi kesalahan saat memuat riwayat transaksi.';
                        }
                    } finally {
                        this.loadingEntries = false;
                        this.loadingMore = false;
                    }
                },

                loadMoreEntries() {
                    if (!this.entriesData.next_cursor || this.loadingMore) return;
                    this.loadEntries(false);
                },

                // Format Helpers
                formatRupiah(amount) {
                    if (amount === null || amount === undefined || isNaN(amount)) return 'Rp 0';
                    const intVal = Math.round(amount);
                    if (intVal === 0) return 'Rp 0';
                    const isNeg = intVal < 0;
                    const formatted = Math.abs(intVal).toLocaleString('id-ID');
                    return isNeg ? `-Rp ${formatted}` : `Rp ${formatted}`;
                },

                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return Number(val).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
                },

                formatRingkas(val) {
                    if (!val || isNaN(val)) return '0';
                    const abs = Math.abs(Math.round(val));
                    if (abs >= 1000) {
                        const inK = Math.round(abs / 1000);
                        return (val < 0 ? '-' : '') + inK.toLocaleString('id-ID') + 'k';
                    }
                    return val.toString();
                },

                formatTime(isoString) {
                    if (!isoString) return '';
                    try {
                        const d = new Date(isoString);
                        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
                    } catch {
                        return '';
                    }
                },

                // Handlers untuk Kerangka Commit 3
                onAddExpense() {
                    alert('Formulir pencatatan pengeluaran (bottom sheet) akan aktif di PRD 4.');
                },

                onExport(type) {
                    alert(`Fitur ekspor berkas pembukuan format ${type.toUpperCase()} akan aktif di PRD 4.`);
                }
            }));
        });
    </script>
</x-app-layout>
