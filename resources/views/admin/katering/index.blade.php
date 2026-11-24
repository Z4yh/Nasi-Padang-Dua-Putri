<x-app-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        .font-serif-title { font-family: 'Playfair Display', Georgia, serif; }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-stone-800 leading-tight">
                    {{ __('Buku Pesanan Katering') }}
                </h2>
                <p class="text-sm text-stone-500 mt-1">
                    Kontrol jadwal pesanan, produksi dapur, status pembayaran, dan pengiriman katering
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('katering.export.pdf') }}" target="_blank"
                   class="inline-flex items-center px-3.5 py-2 bg-white border border-stone-300 rounded-lg font-medium text-xs text-stone-700 hover:bg-stone-50 transition shadow-xs">
                    <svg class="w-4 h-4 mr-1.5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh PDF
                </a>
                <a href="{{ route('katering.export.csv') }}"
                   class="inline-flex items-center px-3.5 py-2 bg-white border border-stone-300 rounded-lg font-medium text-xs text-stone-700 hover:bg-stone-50 transition shadow-xs">
                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh CSV
                </a>
                <button type="button" onclick="openCreateModal()"
                   class="inline-flex items-center px-4 py-2 bg-[#8B1E1E] border border-transparent rounded-lg font-medium text-sm text-white hover:bg-[#701616] focus:bg-[#701616] active:bg-[#581111] transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Pesanan Katering
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#FAF6F0] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Notifikasi Sukses / Error --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-xs flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold ml-2 text-lg">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-xs flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 font-bold ml-2 text-lg">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg shadow-xs">
                    <ul class="list-disc list-inside space-y-0.5 text-xs font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kartu Ringkasan Atas (Stat Cards Grid) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Card 1: Produksi Hari Ini --}}
                <div class="rounded-2xl bg-gradient-to-br from-[#6B1414] via-[#891B1B] to-[#991B1B] text-white p-5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold tracking-wider uppercase text-amber-200">Pesanan Hari Ini</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-semibold">
                            {{ $todayOrderCount }} Pengantaran
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-red-100/90">Total Produksi Hari Ini:</div>
                        <div class="text-3xl font-extrabold leading-tight mt-1">{{ $todayOrderCount }} Pesanan</div>
                        <div class="text-sm font-semibold text-amber-300 mt-1">({{ number_format($todayBoxCount, 0, ',', '.') }} Boks)</div>
                    </div>
                </div>

                {{-- Card 2: Piutang / COD Menunggu Pelunasan --}}
                <div class="rounded-2xl bg-white border border-stone-200/80 p-5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Sisa Piutang / COD</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
                            Menunggu Pelunasan
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-stone-500">Total Tagihan Tertunda:</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-[#8B1E1E] leading-tight mt-1">
                            Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-stone-400 mt-1">Wajib ditagihkan kurir saat serah terima</div>
                    </div>
                </div>

                {{-- Card 3: Ringkasan Status Jadwal --}}
                <div class="rounded-2xl bg-white border border-stone-200/80 p-5 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold tracking-wider uppercase text-stone-500">Ringkasan Jadwal</span>
                        <span class="text-xs font-bold text-stone-700">Total: {{ $counts['semua'] }} Pesanan</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5 mt-1">
                        <div class="p-2.5 rounded-xl bg-orange-50/80 border border-orange-200 text-center">
                            <div class="text-2xl font-extrabold text-orange-700 leading-tight">{{ $counts['terdekat'] }}</div>
                            <div class="text-[11px] font-bold text-orange-700 mt-0.5">Perlu Dimasak/Antar</div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200 text-center">
                            <div class="text-2xl font-extrabold text-emerald-700 leading-tight">{{ $counts['selesai'] }}</div>
                            <div class="text-[11px] font-bold text-emerald-700 mt-0.5">Selesai &amp; Lunas</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Filter & Bar Status --}}
            <div class="bg-white rounded-xl p-2.5 border border-stone-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('katering.index', ['tab' => 'semua']) }}"
                       class="flex-1 sm:flex-none py-2 px-4 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition {{ $tab === 'semua' ? 'bg-[#8B1E1E] text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                        <span>Semua</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'semua' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700' }}">
                            {{ $counts['semua'] }}
                        </span>
                    </a>

                    <a href="{{ route('katering.index', ['tab' => 'terdekat']) }}"
                       class="flex-1 sm:flex-none py-2 px-4 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition {{ $tab === 'terdekat' ? 'bg-[#8B1E1E] text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                        <span>Terdekat &amp; Aktif</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'terdekat' ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700' }}">
                            {{ $counts['terdekat'] }}
                        </span>
                    </a>

                    <a href="{{ route('katering.index', ['tab' => 'selesai']) }}"
                       class="flex-1 sm:flex-none py-2 px-4 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition {{ $tab === 'selesai' ? 'bg-[#8B1E1E] text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                        <span>Selesai</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'selesai' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $counts['selesai'] }}
                        </span>
                    </a>
                </div>

                <div class="text-xs text-stone-500 font-medium">
                    Menampilkan <span class="font-bold text-stone-800">{{ $orders->count() }}</span> pesanan katering
                </div>
            </div>

            {{-- Daftar Kartu Pesanan Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse ($orders as $order)
                    @php
                        $totalMasak = (int) $order->total_boks_pesan + (int) $order->total_boks_bonus;
                        $statusBayar = ((float) $order->sisa_tagihan <= 0 && (float) $order->total_tagihan > 0)
                            ? 'lunas'
                            : ((float) $order->nominal_dp > 0 ? 'dp_terkonfirmasi' : 'belum_dp');

                        $jam = $order->jam_kirim ? \Carbon\Carbon::parse($order->jam_kirim)->format('H.i') . ' WIB' : '-';
                        if (!$order->tanggal_kirim) {
                            $labelJadwal = "[{$jam}]";
                        } elseif ($order->tanggal_kirim->isToday()) {
                            $labelJadwal = "[HARI INI – {$jam}]";
                        } elseif ($order->tanggal_kirim->isYesterday()) {
                            $labelJadwal = "[KEMARIN – {$jam}]";
                        } elseif ($order->tanggal_kirim->isTomorrow()) {
                            $labelJadwal = "[BESOK – {$jam}]";
                        } else {
                            $labelJadwal = '[' . strtoupper($order->tanggal_kirim->translatedFormat('d M Y')) . " – {$jam}]";
                        }

                        $cardBorder = match($order->status_pesanan) {
                            'diproses' => 'border-orange-300',
                            'menunggu_dp' => 'border-amber-300',
                            'siap_kirim' => 'border-blue-300',
                            'selesai' => 'border-emerald-300',
                            'dibatalkan' => 'border-red-300',
                            default => 'border-stone-200',
                        };

                        $timeColor = match($order->status_pesanan) {
                            'diproses' => 'text-orange-600',
                            'menunggu_dp' => 'text-amber-700',
                            'siap_kirim' => 'text-blue-600',
                            'selesai' => 'text-stone-500',
                            'dibatalkan' => 'text-red-600',
                            default => 'text-stone-600',
                        };

                        [$statusLabel, $statusPill] = match($order->status_pesanan) {
                            'diproses' => ['SEDANG DIMASAK', 'bg-amber-100 text-amber-800 border-amber-200'],
                            'menunggu_dp' => ['MENUNGGU DP', 'bg-rose-100 text-rose-700 border-rose-200'],
                            'siap_kirim' => ['SIAP KIRIM', 'bg-blue-100 text-blue-700 border-blue-200'],
                            'selesai' => ['✓ SELESAI & TUNTAS', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                            'dibatalkan' => ['DIBATALKAN', 'bg-red-100 text-red-700 border-red-200'],
                            default => [strtoupper(str_replace('_', ' ', $order->status_pesanan)), 'bg-stone-100 text-stone-700 border-stone-200'],
                        };
                    @endphp

                    <article class="bg-white rounded-2xl p-5 border-2 {{ $cardBorder }} shadow-sm flex flex-col justify-between transition hover:shadow-md">
                        <div>
                            {{-- Jadwal & Badge Status --}}
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-extrabold {{ $timeColor }}">{{ $labelJadwal }}</span>
                                <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-extrabold tracking-wide {{ $statusPill }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Nama Pemesan --}}
                            <h2 class="text-lg font-extrabold text-stone-900 leading-snug mb-1">
                                {{ $order->nama_pemesan }}
                            </h2>

                            {{-- Nama Paket & Jumlah Boks --}}
                            <div class="mb-3">
                                <div class="text-xs font-bold text-stone-800">{{ $order->nama_paket ?? 'Paket Katering Tiga Putri' }}</div>
                                <div class="text-xs text-stone-500 mt-0.5">
                                    Jumlah: <strong class="text-stone-700">{{ $order->total_boks_pesan }} Boks</strong>
                                    @if ($order->total_boks_bonus > 0)
                                        (+ {{ $order->total_boks_bonus }} {{ $order->status_pesanan === 'selesai' ? 'Bonus' : 'Boks Bonus' }})
                                    @endif
                                    @if ($order->status_pesanan === 'selesai')
                                        &rarr; <strong class="text-stone-800">Total Masak: {{ $totalMasak }} Boks</strong>
                                    @endif
                                </div>
                            </div>

                            {{-- Alamat & Metode Antar --}}
                            <div class="text-xs text-stone-600 mb-3 leading-relaxed">
                                <span>{{ $order->alamat_pengiriman ?? 'Alamat pengantaran belum diisi' }}</span>
                                <span class="inline-block ml-1 px-1.5 py-0.5 rounded bg-stone-100 text-stone-700 font-bold text-[10px]">
                                    [{{ $order->metode_antar ?? 'Diantar' }}]
                                </span>
                            </div>

                            {{-- Box Catatan / Status Arsip Dapur --}}
                            @if ($order->status_pesanan === 'selesai')
                                <div class="rounded-xl bg-stone-50 border border-stone-200 p-3 mb-3 text-xs">
                                    <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">
                                        <span>Status Arsip Dapur</span>
                                        <span>ID: {{ $order->nomor_pesanan }}</span>
                                    </div>
                                    <p class="text-stone-700 leading-relaxed">
                                        <strong class="text-stone-900">Bukti Selesai:</strong>
                                        {{ $order->bukti_selesai ?? 'Pesanan telah diterima pelanggan dengan baik.' }}
                                    </p>
                                </div>
                            @else
                                <div class="rounded-xl bg-amber-50/70 border border-amber-200/80 p-3 mb-3 text-xs">
                                    <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-amber-800 mb-1">
                                        <span>Catatan</span>
                                        <span class="text-stone-500">ID: {{ $order->nomor_pesanan }}</span>
                                    </div>
                                    @if (!empty($order->info_kurir))
                                        <p class="text-stone-700 mb-1">
                                            <strong class="text-stone-900">Kurir:</strong> {{ $order->info_kurir }}
                                        </p>
                                    @endif
                                    <p class="text-stone-700 leading-relaxed">
                                        <strong class="text-stone-900">Instruksi:</strong>
                                        {{ $order->catatan_dapur ?? 'Tidak ada catatan khusus dapur.' }}
                                    </p>
                                </div>
                            @endif

                            {{-- Rincian Pembayaran --}}
                            @if ($statusBayar === 'lunas' && $order->status_pesanan === 'selesai')
                                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 mb-4 flex items-center justify-between text-xs">
                                    <span class="font-bold text-emerald-800">Pelunasan<br>Selesai</span>
                                    <span class="text-stone-500 font-semibold">({{ strtoupper($order->metode_bayar ?? 'COD') }})</span>
                                    <span class="font-extrabold text-emerald-700 text-right">
                                        Rp {{ number_format($order->total_tagihan, 0, ',', '.') }}<br>[LUNAS]
                                    </span>
                                </div>
                            @elseif ($statusBayar === 'lunas')
                                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 mb-4 flex items-center justify-between gap-2 text-xs">
                                    <span class="font-bold text-emerald-900">Status<br>Pembayaran:</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-extrabold text-[11px] text-right">
                                        LUNAS {{ strtoupper($order->metode_bayar ?? 'TRANSFER') }} (Rp {{ number_format($order->total_tagihan, 0, ',', '.') }})
                                    </span>
                                </div>
                            @else
                                <div class="rounded-xl bg-stone-50 border border-stone-200/80 p-3 mb-4 text-xs space-y-1.5">
                                    <div class="flex justify-between items-center">
                                        <span class="text-stone-500">Total Tagihan:</span>
                                        <span class="font-extrabold text-stone-900">Rp {{ number_format($order->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-stone-500">
                                            DP Diterima{{ $order->nominal_dp > 0 ? ' (' . ucfirst($order->metode_bayar ?? 'Transfer') . ')' : '' }}:
                                        </span>
                                        @if ($order->nominal_dp > 0)
                                            <span class="font-bold text-emerald-700">- Rp {{ number_format($order->nominal_dp, 0, ',', '.') }} (Terkonfirmasi)</span>
                                        @else
                                            <span class="font-bold text-rose-600">Rp 0 (Belum DP)</span>
                                        @endif
                                    </div>
                                    <div class="pt-1.5 border-t border-stone-200 flex justify-between items-start gap-2">
                                        <span class="font-bold text-amber-800 leading-tight">
                                            {{ $order->nominal_dp > 0 ? 'Sisa Tagihan (Wajib COD Saat Antar):' : 'Sisa Tagihan Penuh:' }}
                                        </span>
                                        <span class="font-extrabold text-amber-700 text-right leading-tight">
                                            Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }} [BELUM LUNAS]
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Aksi di Bagian Bawah Kartu --}}
                        <div class="pt-2 border-t border-stone-100">
                            @if ($order->status_pesanan === 'selesai')
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('katering.nota', $order) }}"
                                       class="py-2 px-3 rounded-lg border border-stone-300 bg-white hover:bg-stone-50 text-stone-800 text-xs font-bold text-center transition">
                                        Lihat Nota
                                    </a>
                                    <form action="{{ route('katering.archive', $order) }}" method="POST" onsubmit="return confirm('Arsipkan pesanan {{ $order->nomor_pesanan }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full py-2 px-3 rounded-lg bg-stone-200 hover:bg-stone-300 text-stone-700 text-xs font-bold transition">
                                            Arsipkan
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="grid grid-cols-12 gap-1.5 items-center">
                                    {{-- Dropdown Status --}}
                                    @php
                                        $nextStatuses = $order->statusBerikutnya();
                                        $isLocked = empty($nextStatuses) || in_array($order->status_pesanan, ['selesai', 'dibatalkan']);
                                        $labels = \App\Models\KateringOrder::daftarLabelStatus();
                                    @endphp
                                    <form action="{{ route('katering.status', $order) }}" method="POST" class="col-span-5">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status_pesanan" onchange="this.form.submit()" {{ $isLocked ? 'disabled' : '' }}
                                            class="w-full py-2 px-2 rounded-lg border border-stone-300 {{ $isLocked ? 'bg-stone-100 text-stone-500 cursor-not-allowed' : 'bg-white text-stone-800' }} text-[11px] font-bold focus:outline-none focus:border-red-800">
                                            {{-- Status saat ini (selalu terpilih) --}}
                                            <option value="{{ $order->status_pesanan }}" selected>
                                                {{ $labels[$order->status_pesanan] ?? ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                                            </option>
                                            {{-- Status berikutnya yang diizinkan sesuai aturan urutan --}}
                                            @foreach ($nextStatuses as $nextOpt)
                                                <option value="{{ $nextOpt }}">
                                                    {{ $labels[$nextOpt] ?? ucfirst(str_replace('_', ' ', $nextOpt)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

                                    {{-- Edit Data --}}
                                    <button type="button" onclick='openEditModal(@json($order))'
                                        class="col-span-3 py-2 px-1.5 rounded-lg border border-amber-400 bg-white hover:bg-amber-50 text-amber-800 text-[11px] font-bold transition text-center">
                                        Edit
                                    </button>

                                    {{-- Tombol Utama Kontekstual --}}
                                    <div class="col-span-4">
                                        @if ($statusBayar === 'belum_dp')
                                            <button type="button" onclick="openDpModal({{ $order->id_katering }}, '{{ $order->nomor_pesanan }}', {{ $order->total_tagihan }})"
                                                class="w-full py-2 px-1.5 rounded-lg bg-[#8B1E1E] hover:bg-[#701616] text-white text-[11px] font-bold transition text-center">
                                                Konfirm DP
                                            </button>
                                        @elseif ($statusBayar === 'dp_terkonfirmasi')
                                            <form action="{{ route('katering.payment', $order) }}" method="POST" onsubmit="return confirm('Tandai tagihan {{ $order->nomor_pesanan }} sudah LUNAS?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="lunas">
                                                <button type="submit"
                                                    class="w-full py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-[11px] font-bold transition text-center">
                                                    Lunas
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" onclick="openCompleteModal({{ $order->id_katering }}, '{{ $order->nomor_pesanan }}')"
                                                class="w-full py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-[11px] font-bold transition text-center">
                                                Selesai
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-stone-200">
                        <svg class="w-12 h-12 mx-auto text-stone-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-base font-bold text-stone-700">Belum ada pesanan katering pada tab ini.</p>
                        <p class="text-xs text-stone-500 mt-1">Klik tombol "+ Catat Pesanan Katering" di atas untuk menambahkan jadwal pesanan baru.</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            <footer class="text-center pt-8 border-t border-stone-200/80 text-xs text-stone-500 space-y-1">
                <div class="font-bold text-[#8B1E1E]">Rumah Makan Padang Tiga Putri</div>
                <div>Citarasa Minang Autentik &amp; Legendaris &bull; Sejak 2009</div>
                <div class="text-[11px] text-stone-400">Buku Kontrol Digital Internal Pemilik &amp; Operasional Dapur Sukatani</div>
            </footer>
        </div>
    </div>

    {{-- MODAL 1: TAMBAH / EDIT PESANAN KATERING --}}
    <div id="orderModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] overflow-y-auto p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200 mb-4">
                <h3 id="orderModalTitle" class="font-serif-title font-bold text-xl text-[#8B1E1E]">Catat Pesanan Katering</h3>
                <button type="button" onclick="closeModal('orderModal')" class="text-stone-400 hover:text-stone-700 text-2xl font-bold">&times;</button>
            </div>

            <form id="orderForm" action="{{ route('katering.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="_method" id="orderFormMethod" value="POST">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nama Pemesan *</label>
                        <input type="text" name="nama_pemesan" id="f_nama_pemesan" required placeholder="Bpk. Hendra"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="no_whatsapp" id="f_no_whatsapp" placeholder="0812xxxxxxxx"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nama Paket Katering *</label>
                    <input type="text" name="nama_paket" id="f_nama_paket" required placeholder="Paket Nasi Rendang Komplit"
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Jumlah Boks *</label>
                        <input type="number" name="total_boks_pesan" id="f_total_boks_pesan" required min="1" value="50" oninput="hitungTotal()"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Boks Bonus</label>
                        <input type="number" name="total_boks_bonus" id="f_total_boks_bonus" min="0" value="0"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Harga/Boks *</label>
                        <input type="number" name="harga_satuan" id="f_harga_satuan" min="0" value="27500" oninput="hitungTotal()"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Tanggal Kirim *</label>
                        <input type="date" name="tanggal_kirim" id="f_tanggal_kirim" required
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Jam Kirim *</label>
                        <input type="time" name="jam_kirim" id="f_jam_kirim" required
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Metode Antar *</label>
                        <input type="text" name="metode_antar" id="f_metode_antar" required value="Diantar"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Info Kurir &amp; Plat Kendaraan</label>
                        <input type="text" name="info_kurir" id="f_info_kurir" placeholder="Kang Asep Pick-up Box (Plat B 4812 FOO)"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Alamat Pengantaran *</label>
                    <textarea name="alamat_pengiriman" id="f_alamat_pengiriman" rows="2" required placeholder="Alamat lengkap lokasi pengantaran acara..."
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">Instruksi Khusus Dapur</label>
                    <textarea name="catatan_dapur" id="f_catatan_dapur" rows="2" placeholder="Catatan tingkat pedas, sambal dipisah per bungkus, sendok, tanpa jengkol..."
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Ongkir (Rp)</label>
                        <input type="number" name="ongkos_kirim" id="f_ongkos_kirim" min="0" value="0" oninput="hitungTotal()"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Total Tagihan *</label>
                        <input type="number" name="total_tagihan" id="f_total_tagihan" required min="0" value="1375000"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none font-bold text-stone-800">
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">DP Masuk (Rp)</label>
                        <input type="number" name="nominal_dp" id="f_nominal_dp" min="0" value="0"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Metode Bayar *</label>
                        <select name="metode_bayar" id="f_metode_bayar"
                            class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                            <option value="transfer">Transfer Bank</option>
                            <option value="cod">COD (Bayar Saat Antar)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Bukti Bayar (Opsional)</label>
                        <input type="file" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full text-xs text-stone-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:bg-stone-100 file:font-semibold">
                    </div>
                </div>

                <div class="pt-4 border-t border-stone-200 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('orderModal')"
                        class="px-5 py-2.5 rounded-lg border border-stone-300 font-bold text-stone-700 hover:bg-stone-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#8B1E1E] hover:bg-[#701616] text-white font-bold transition shadow-sm">
                        Simpan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: KONFIRMASI DP --}}
    <div id="dpModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200 mb-4">
                <h3 class="font-bold text-base text-[#8B1E1E]">Konfirmasi DP – <span id="dpOrderCode"></span></h3>
                <button type="button" onclick="closeModal('dpModal')" class="text-stone-400 hover:text-stone-700 font-bold text-xl">&times;</button>
            </div>
            <form id="dpForm" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Nominal DP Masuk (Rp) *</label>
                    <input type="number" name="nominal_dp" id="dpAmountInput" required min="1"
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Metode Pembayaran *</label>
                    <select name="metode_bayar" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-red-800 focus:outline-none">
                        <option value="transfer">Transfer Bank</option>
                        <option value="cod">Tunai / COD</option>
                    </select>
                </div>
                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('dpModal')" class="px-4 py-2 rounded-lg border border-stone-300 font-bold text-stone-700 hover:bg-stone-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-[#8B1E1E] hover:bg-[#701616] text-white font-bold">Simpan DP</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 3: TANDAI SELESAI --}}
    <div id="completeModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200 mb-4">
                <h3 class="font-bold text-base text-emerald-800">Tandai Selesai – <span id="completeOrderCode"></span></h3>
                <button type="button" onclick="closeModal('completeModal')" class="text-stone-400 hover:text-stone-700 font-bold text-xl">&times;</button>
            </div>
            <form id="completeForm" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block font-bold text-stone-700 mb-1">Catatan Bukti Pengiriman &amp; Penerima *</label>
                    <textarea name="bukti_selesai" rows="3" required
                        placeholder="Contoh: Makanan tiba pukul 11.45 WIB, diterima Ibu Rahmat, nota bertanda tangan."
                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-xs focus:border-emerald-700 focus:outline-none"></textarea>
                </div>
                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" onclick="closeModal('completeModal')" class="px-4 py-2 rounded-lg border border-stone-300 font-bold text-stone-700 hover:bg-stone-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function hitungTotal() {
            const boks = parseInt(document.getElementById('f_total_boks_pesan').value) || 0;
            const harga = parseInt(document.getElementById('f_harga_satuan').value) || 0;
            const ongkir = parseInt(document.getElementById('f_ongkos_kirim').value) || 0;
            document.getElementById('f_total_tagihan').value = (boks * harga) + ongkir;
        }

        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function openCreateModal() {
            document.getElementById('orderModalTitle').innerText = 'Catat Pesanan Katering';
            document.getElementById('orderForm').action = "{{ route('katering.store') }}";
            document.getElementById('orderFormMethod').value = 'POST';
            document.getElementById('orderForm').reset();
            hitungTotal();
            openModal('orderModal');
        }

        function openEditModal(order) {
            document.getElementById('orderModalTitle').innerText = 'Edit Pesanan ' + order.nomor_pesanan;
            document.getElementById('orderForm').action = '/katering/' + order.id_katering;
            document.getElementById('orderFormMethod').value = 'PUT';

            document.getElementById('f_nama_pemesan').value = order.nama_pemesan || '';
            document.getElementById('f_no_whatsapp').value = order.no_whatsapp || '';
            document.getElementById('f_nama_paket').value = order.nama_paket || '';
            document.getElementById('f_total_boks_pesan').value = order.total_boks_pesan || 1;
            document.getElementById('f_total_boks_bonus').value = order.total_boks_bonus || 0;
            document.getElementById('f_harga_satuan').value = parseInt(order.harga_satuan || 0);
            document.getElementById('f_tanggal_kirim').value = order.tanggal_kirim ? order.tanggal_kirim.slice(0, 10) : '';
            document.getElementById('f_jam_kirim').value = order.jam_kirim ? order.jam_kirim.slice(0, 5) : '';
            document.getElementById('f_metode_antar').value = order.metode_antar || 'Diantar';
            document.getElementById('f_alamat_pengiriman').value = order.alamat_pengiriman || '';
            document.getElementById('f_info_kurir').value = order.info_kurir || '';
            document.getElementById('f_catatan_dapur').value = order.catatan_dapur || '';
            document.getElementById('f_ongkos_kirim').value = parseInt(order.ongkos_kirim || 0);
            document.getElementById('f_total_tagihan').value = parseInt(order.total_tagihan || 0);
            document.getElementById('f_nominal_dp').value = parseInt(order.nominal_dp || 0);
            document.getElementById('f_metode_bayar').value = order.metode_bayar || 'transfer';

            openModal('orderModal');
        }

        function openDpModal(id, code, totalAmount) {
            document.getElementById('dpOrderCode').innerText = code;
            document.getElementById('dpForm').action = '/katering/' + id + '/payment';
            document.getElementById('dpAmountInput').value = Math.round(totalAmount * 0.5);
            openModal('dpModal');
        }

        function openCompleteModal(id, code) {
            document.getElementById('completeOrderCode').innerText = code;
            document.getElementById('completeForm').action = '/katering/' + id + '/complete';
            openModal('completeModal');
        }
    </script>
</x-app-layout>
