<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-stone-800 leading-tight">
                    Riwayat Transaksi Katering
                </h2>
                <p class="text-sm text-stone-500 mt-1">
                    Cari dan filter seluruh pesanan katering yang pernah masuk
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#FAF6F0] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ============================================================ --}}
            {{-- FORM FILTER: Pencarian, Status, Tanggal                       --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-xl border border-stone-200/80 shadow-xs p-5">
                <form method="GET" action="{{ route('riwayat.index') }}" class="space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                        {{-- Kolom Pencarian --}}
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Cari Pesanan</label>
                            <input
                                type="text"
                                name="cari"
                                value="{{ $cari }}"
                                placeholder="Nomor pesanan / nama pemesan..."
                                class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#8B1E1E] focus:outline-none"
                            >
                        </div>

                        {{-- Dropdown Filter Status --}}
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Filter Status</label>
                            <select
                                name="status"
                                class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#8B1E1E] focus:outline-none"
                            >
                                {{-- Opsi "Semua" (tidak ada filter status) --}}
                                <option value="">Semua Status</option>
                                @foreach ($daftarStatus as $nilai => $label)
                                    <option value="{{ $nilai }}" {{ $status === $nilai ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Tanggal: Dari --}}
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Tanggal Kirim (Dari)</label>
                            <input
                                type="date"
                                name="dari"
                                value="{{ $dariBln }}"
                                class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#8B1E1E] focus:outline-none"
                            >
                        </div>

                        {{-- Filter Tanggal: Sampai --}}
                        <div>
                            <label class="block text-xs font-bold text-stone-600 mb-1">Tanggal Kirim (Sampai)</label>
                            <input
                                type="date"
                                name="sampai"
                                value="{{ $sampai }}"
                                class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#8B1E1E] focus:outline-none"
                            >
                        </div>
                    </div>

                    {{-- Tombol Cari & Reset --}}
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="px-5 py-2 bg-[#8B1E1E] hover:bg-[#701616] text-white text-sm font-bold rounded-lg transition shadow-xs"
                        >
                            Cari
                        </button>
                        {{-- Tombol Reset: arahkan ke halaman riwayat tanpa parameter apapun --}}
                        <a
                            href="{{ route('riwayat.index') }}"
                            class="px-5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 text-sm font-bold rounded-lg transition"
                        >
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- ============================================================ --}}
            {{-- TABEL RIWAYAT PESANAN                                         --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-xl border border-stone-200/80 shadow-xs overflow-hidden">

                {{-- Info jumlah hasil --}}
                <div class="px-5 py-3 border-b border-stone-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500">
                        Menampilkan <span class="text-stone-800 font-bold">{{ $orders->total() }}</span> pesanan
                    </span>
                </div>

                @if ($orders->isEmpty())
                    {{-- Pesan kosong kalau tidak ada data --}}
                    <div class="py-16 text-center">
                        <svg class="w-12 h-12 mx-auto text-stone-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-base font-bold text-stone-700">Tidak ada data pesanan</p>
                        <p class="text-xs text-stone-500 mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                    </div>
                @else
                    {{-- Wrapper scroll horizontal untuk layar kecil --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-stone-50 border-b border-stone-200">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">No. Pesanan</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Nama Pemesan</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Tanggal Kirim</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold text-stone-500 uppercase tracking-wider">Total Tagihan</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-stone-500 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-stone-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach ($orders as $order)
                                    @php
                                        // Tentukan warna badge berdasarkan status pesanan
                                        $badgeKelas = match ($order->status_pesanan) {
                                            'menunggu_dp' => 'bg-rose-100 text-rose-700 border border-rose-200',
                                            'diproses'    => 'bg-amber-100 text-amber-800 border border-amber-200',
                                            'siap_kirim'  => 'bg-blue-100 text-blue-700 border border-blue-200',
                                            'selesai'     => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'dibatalkan'  => 'bg-red-100 text-red-700 border border-red-200',
                                            default       => 'bg-stone-100 text-stone-700 border border-stone-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-stone-50/70 transition">
                                        <td class="px-5 py-3.5 font-mono text-xs text-stone-700 font-semibold whitespace-nowrap">
                                            {{ $order->nomor_pesanan }}
                                        </td>
                                        <td class="px-5 py-3.5 font-semibold text-stone-800 whitespace-nowrap">
                                            {{ $order->nama_pemesan }}
                                        </td>
                                        <td class="px-5 py-3.5 text-stone-600 whitespace-nowrap">
                                            {{-- Format tanggal: Senin, 25 Nov 2026 --}}
                                            {{ $order->tanggal_kirim ? $order->tanggal_kirim->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold text-stone-900 whitespace-nowrap">
                                            Rp {{ number_format($order->total_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeKelas }}">
                                                {{ $daftarStatus[$order->status_pesanan] ?? $order->status_pesanan }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            <a
                                                href="{{ route('riwayat.show', $order->id_katering) }}"
                                                class="inline-flex items-center px-3.5 py-1.5 bg-[#8B1E1E] hover:bg-[#701616] text-white text-xs font-bold rounded-lg transition shadow-xs"
                                            >
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi — withQueryString() sudah dipanggil di controller --}}
                    @if ($orders->hasPages())
                        <div class="px-5 py-4 border-t border-stone-100">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- Footer --}}
            <footer class="text-center pt-4 border-t border-stone-200/80 text-xs text-stone-500 space-y-1">
                <div class="font-bold text-[#8B1E1E]">Rumah Makan Padang Tiga Putri</div>
                <div>Citarasa Minang Autentik &amp; Legendaris &bull; Sejak 2009</div>
            </footer>

        </div>
    </div>
</x-app-layout>
