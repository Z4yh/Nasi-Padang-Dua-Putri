<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas - {{ $summary['label'] ?? 'RM Padang Tiga Putri' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            DEFAULT: '#7B1113',
                            dark: '#630E10',
                            light: '#FAF6F0'
                        },
                        emerald: {
                            custom: '#10996D'
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm 12mm;
            }
            body {
                background: #FFFFFF !important;
                color: #1c1917 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
            .page-break {
                page-break-before: always;
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #FAF6F0;
            color: #292524;
        }

        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }
    </style>
</head>
<body class="min-h-screen py-4 sm:py-8">

    {{-- Top Action Bar (Hidden during Print) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 px-4">
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-md p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center text-[#7B1113] font-bold">
                    🖨️
                </span>
                <div>
                    <h1 class="text-sm font-bold text-stone-800">Pratinjau Cetak / Ekspor PDF</h1>
                    <p class="text-xs text-stone-500">Gunakan opsi "Save as PDF" pada dialog printer peramban.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button
                    type="button"
                    onclick="window.print()"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-[#7B1113] hover:bg-[#630E10] text-white rounded-xl text-xs font-bold transition shadow-xs"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / Simpan PDF
                </button>

                <a
                    href="{{ url('/keuangan?period=' . $period) }}"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-stone-50 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold transition shadow-2xs"
                >
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Printable Paper Container (A4 Form Factor) --}}
    <main class="max-w-4xl mx-auto bg-white sm:rounded-3xl sm:border sm:border-stone-200/90 sm:shadow-lg p-6 sm:p-10 space-y-6">

        {{-- 1. HEADER USAHA --}}
        <header class="border-b-2 border-stone-800/80 pb-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <div>
                    <div class="flex items-center gap-2 text-[#C59B27] text-xs font-bold uppercase tracking-wider mb-1">
                        <span>❖</span>
                        <span>Rumah Makan Masakan Padang</span>
                        <span>❖</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold font-serif-title text-[#7B1113] tracking-wide uppercase">
                        RM PADANG TIGA PUTRI
                    </h1>
                    <p class="text-xs text-stone-600 mt-1">
                        Jl. Raya Sukatani, Bekasi, Jawa Barat • Telp / WA: 0812-8877-6655
                    </p>
                </div>

                <div class="text-left sm:text-right text-xs space-y-1">
                    <div class="inline-block px-2.5 py-1 bg-stone-100 rounded-lg font-bold text-stone-800 uppercase tracking-wide">
                        Laporan Arus Kas
                    </div>
                    <div class="text-stone-600">
                        Periode: <strong class="text-stone-900">{{ $summary['label'] ?? '-' }}</strong>
                    </div>
                    <div class="text-stone-500 text-[11px]">
                        Dicetak: {{ $printedAt->translatedFormat('d M Y, H:i') }} WIB
                    </div>
                </div>
            </div>
        </header>

        {{-- 2. RINGKASAN KINERJA KEUANGAN --}}
        <section class="space-y-3">
            <h2 class="text-xs font-bold text-stone-500 uppercase tracking-wider">
                Ringkasan Arus Kas
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                {{-- Laba Bersih --}}
                <div class="bg-[#FAF6F0] rounded-xl p-3 border border-stone-200">
                    <div class="text-stone-500 text-[11px] font-medium">Laba Bersih Usaha</div>
                    <div class="text-base font-extrabold text-[#7B1113] mt-1">
                        Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-stone-500 mt-0.5">
                        Margin: <strong class="text-stone-700">{{ number_format($summary['margin_pct'] ?? 0, 1, ',', '.') }}%</strong>
                    </div>
                </div>

                {{-- Total Kas Masuk --}}
                <div class="bg-emerald-50/60 rounded-xl p-3 border border-emerald-100">
                    <div class="text-emerald-700 text-[11px] font-medium">Total Kas Masuk (+)</div>
                    <div class="text-base font-extrabold text-emerald-700 mt-1">
                        Rp {{ number_format($summary['total_in'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-emerald-600 mt-0.5">
                        POS + Katering
                    </div>
                </div>

                {{-- Total Kas Keluar --}}
                <div class="bg-rose-50/60 rounded-xl p-3 border border-rose-100">
                    <div class="text-rose-700 text-[11px] font-medium">Total Kas Keluar (–)</div>
                    <div class="text-base font-extrabold text-rose-700 mt-1">
                        Rp {{ number_format($summary['total_out'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-rose-600 mt-0.5">
                        Belanja Pasar &amp; Operasional
                    </div>
                </div>

                {{-- Komposisi Kasir POS --}}
                <div class="bg-white rounded-xl p-3 border border-stone-200">
                    <div class="text-stone-500 text-[11px] font-medium">Kasir POS (Warung)</div>
                    <div class="text-base font-extrabold text-stone-800 mt-1">
                        Rp {{ number_format($summary['sources']['pos']['total'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-stone-500 mt-0.5">
                        {{ $summary['sources']['pos']['tx_count'] ?? 0 }} Transaksi Terlayani
                    </div>
                </div>
            </div>

            {{-- Breakdown Baris Kecil --}}
            <div class="bg-stone-50 rounded-xl p-3 border border-stone-200 text-xs flex flex-wrap items-center justify-between gap-y-2 gap-x-4">
                <div class="flex items-center gap-2">
                    <span class="text-stone-500">Pemasukan:</span>
                    <span class="font-semibold text-stone-800">POS Rp {{ number_format($summary['in_breakdown']['pos'] ?? 0, 0, ',', '.') }}</span>
                    <span class="text-stone-400">•</span>
                    <span class="font-semibold text-stone-800">Katering Rp {{ number_format($summary['in_breakdown']['catering'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-stone-500">Pengeluaran:</span>
                    <span class="font-semibold text-stone-800">Pasar Rp {{ number_format($summary['out_breakdown']['pasar_subuh'] ?? 0, 0, ',', '.') }}</span>
                    <span class="text-stone-400">•</span>
                    <span class="font-semibold text-stone-800">Operasional Rp {{ number_format($summary['out_breakdown']['operasional'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        {{-- 3. TABEL DETAIL TRANSAKSI --}}
        <section class="space-y-2 pt-2">
            <div class="flex justify-between items-center">
                <h2 class="text-xs font-bold text-stone-500 uppercase tracking-wider">
                    Rincian Riwayat Transaksi ({{ count($entries) }} Catatan)
                </h2>
                <span class="text-[11px] text-stone-400">
                    Filter: {{ $type === 'in' ? 'Hanya Kas Masuk' : ($type === 'out' ? 'Hanya Kas Keluar' : 'Semua Transaksi') }}
                </span>
            </div>

            <div class="border border-stone-300 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-stone-100 border-b border-stone-300 text-stone-700 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 w-28">Waktu</th>
                            <th class="py-2.5 px-3 w-20 text-center">Jenis</th>
                            <th class="py-2.5 px-3">Kategori &amp; Rincian Transaksi</th>
                            <th class="py-2.5 px-3 w-28">Metode</th>
                            <th class="py-2.5 px-3 w-32 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($entries as $index => $item)
                            @php
                                $occurredAt = $item['occurred_at'] instanceof \Carbon\CarbonInterface
                                    ? $item['occurred_at']
                                    : \Carbon\Carbon::parse($item['occurred_at']);
                                $isMasuk = $item['type'] === 'in';
                            @endphp
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-[#FAF6F0]/40' }}">
                                <td class="py-2 px-3 text-center text-stone-400 font-mono text-[11px]">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-stone-600 text-[11px]">
                                    <div>{{ $occurredAt->translatedFormat('d M Y') }}</div>
                                    <div class="text-stone-400">{{ $occurredAt->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $isMasuk ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $isMasuk ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    <div class="font-bold text-stone-800">{{ $item['title'] }}</div>
                                    @if (!empty($item['subtitle']))
                                        <div class="text-[11px] text-stone-500 mt-0.5">{{ $item['subtitle'] }}</div>
                                    @endif
                                </td>
                                <td class="py-2 px-3 uppercase text-[11px] font-semibold text-stone-600">
                                    {{ $item['method'] ?? '-' }}
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap font-bold font-mono text-xs {{ $isMasuk ? 'text-emerald-700' : 'text-[#7B1113]' }}">
                                    {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 italic">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-stone-100 border-t-2 border-stone-300 font-bold text-stone-800">
                        <tr>
                            <td colspan="5" class="py-2.5 px-3 text-right text-xs">
                                TOTAL KAS MASUK:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700">
                                +Rp {{ number_format($summary['total_in'] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="py-2.5 px-3 text-right text-xs">
                                TOTAL KAS KELUAR:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-[#7B1113]">
                                -Rp {{ number_format($summary['total_out'] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="bg-stone-200 text-stone-900 text-sm">
                            <td colspan="5" class="py-2.5 px-3 text-right font-extrabold">
                                LABA BERSIH USAHA:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-[#7B1113]">
                                Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        {{-- 4. TANDA TANGAN & PENGESAHAN DOKUMEN --}}
        <footer class="pt-6 border-t border-stone-200">
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <div class="space-y-14">
                    <p class="text-stone-500">Dibuat Oleh,</p>
                    <div class="border-t border-stone-400 w-40 mx-auto pt-1 font-bold text-stone-800">
                        Admin Keuangan
                    </div>
                </div>

                <div class="space-y-14">
                    <p class="text-stone-500">Mengetahui / Menyetujui,</p>
                    <div class="border-t border-stone-400 w-40 mx-auto pt-1 font-bold text-stone-800">
                        Pemilik RM Padang Tiga Putri
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-[10px] text-stone-400 italic">
                Dokumen resmi rekapitulasi arus kas internal • RM Padang Tiga Putri Sukatani, Bekasi
            </div>
        </footer>

    </main>

</body>
</html>
