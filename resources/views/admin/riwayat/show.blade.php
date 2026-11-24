<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                {{-- Tombol kembali ke halaman riwayat --}}
                <a
                    href="{{ route('riwayat.index') }}"
                    class="inline-flex items-center text-xs font-semibold text-stone-500 hover:text-[#8B1E1E] mb-2 transition"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Riwayat
                </a>
                <h2 class="font-semibold text-2xl text-stone-800 leading-tight">
                    Detail Pesanan: {{ $riwayat->nomor_pesanan }}
                </h2>
                <p class="text-sm text-stone-500 mt-1">
                    Rincian lengkap pesanan katering atas nama {{ $riwayat->nama_pemesan }}
                </p>
            </div>

            {{-- Badge status pesanan --}}
            @php
                $badgeKelas = match ($riwayat->status_pesanan) {
                    'menunggu_dp' => 'bg-rose-100 text-rose-700 border border-rose-200',
                    'diproses'    => 'bg-amber-100 text-amber-800 border border-amber-200',
                    'siap_kirim'  => 'bg-blue-100 text-blue-700 border border-blue-200',
                    'selesai'     => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                    'dibatalkan'  => 'bg-red-100 text-red-700 border border-red-200',
                    default       => 'bg-stone-100 text-stone-700 border border-stone-200',
                };
            @endphp
            <span class="px-4 py-1.5 rounded-full text-sm font-extrabold {{ $badgeKelas }}">
                {{ $daftarStatus[$riwayat->status_pesanan] ?? $riwayat->status_pesanan }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 bg-[#FAF6F0] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ============================================================ --}}
            {{-- KARTU INFORMASI PESANAN                                        --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-xl border border-stone-200/80 shadow-xs p-6">
                <h3 class="text-sm font-extrabold text-stone-500 uppercase tracking-wider mb-4">
                    Informasi Pesanan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Nomor Pesanan</div>
                        <div class="font-mono font-bold text-stone-800">{{ $riwayat->nomor_pesanan }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Nama Pemesan</div>
                        <div class="font-bold text-stone-800">{{ $riwayat->nama_pemesan }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">No. WhatsApp</div>
                        <div class="text-stone-700">{{ $riwayat->no_whatsapp ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Tanggal Kirim</div>
                        <div class="text-stone-700">
                            {{ $riwayat->tanggal_kirim ? $riwayat->tanggal_kirim->translatedFormat('l, d F Y') : '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Jam Kirim</div>
                        <div class="text-stone-700">
                            {{ $riwayat->jam_kirim ? \Carbon\Carbon::parse($riwayat->jam_kirim)->format('H.i') . ' WIB' : '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Jumlah Boks</div>
                        <div class="text-stone-700">
                            {{ $riwayat->total_boks_pesan }} boks
                            @if ($riwayat->total_boks_bonus > 0)
                                <span class="text-stone-400">(+ {{ $riwayat->total_boks_bonus }} bonus)</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Status Pesanan</div>
                        <div class="font-bold text-stone-800">
                            {{ $daftarStatus[$riwayat->status_pesanan] ?? $riwayat->status_pesanan }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-stone-400 font-semibold mb-0.5">Tanggal Dicatat</div>
                        <div class="text-stone-700">
                            {{ $riwayat->created_at ? $riwayat->created_at->translatedFormat('d F Y, H:i') : '-' }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- KARTU RINCIAN PEMBAYARAN                                       --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-xl border border-stone-200/80 shadow-xs p-6">
                <h3 class="text-sm font-extrabold text-stone-500 uppercase tracking-wider mb-4">
                    Rincian Pembayaran
                </h3>
                <div class="space-y-2 text-sm">

                    <div class="flex justify-between items-center py-2 border-b border-stone-100">
                        <span class="text-stone-500">Total Tagihan</span>
                        <span class="font-bold text-stone-900">
                            Rp {{ number_format($riwayat->total_tagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-stone-100">
                        <span class="text-stone-500">DP Diterima</span>
                        <span class="font-bold {{ $riwayat->nominal_dp > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            Rp {{ number_format($riwayat->nominal_dp, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-2">
                        <span class="font-bold text-stone-700">Sisa Tagihan</span>
                        <span class="font-extrabold {{ $riwayat->sisa_tagihan <= 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                            @if ($riwayat->sisa_tagihan <= 0)
                                LUNAS
                            @else
                                Rp {{ number_format($riwayat->sisa_tagihan, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>

                    @if ($riwayat->bukti_bayar)
                        <div class="pt-3 border-t border-stone-100">
                            <div class="text-xs text-stone-400 font-semibold mb-1.5">Bukti Bayar</div>
                            {{-- Tampilkan nama file bukti bayar (bukan gambar, agar sederhana) --}}
                            <span class="text-xs text-stone-600 bg-stone-100 px-2.5 py-1 rounded-lg font-mono">
                                {{ basename($riwayat->bukti_bayar) }}
                            </span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TABEL RINCIAN MENU (dari relasi details().menu)                --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-xl border border-stone-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-stone-100">
                    <h3 class="text-sm font-extrabold text-stone-500 uppercase tracking-wider">
                        Rincian Menu Pesanan
                    </h3>
                </div>

                @if ($riwayat->details->isEmpty())
                    {{-- Kalau belum ada detail menu yang dicatat --}}
                    <div class="py-10 text-center text-sm text-stone-500">
                        <p>Belum ada rincian menu yang dicatat untuk pesanan ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-stone-50 border-b border-stone-200">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-stone-500 uppercase tracking-wider">Menu</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold text-stone-500 uppercase tracking-wider">Harga Satuan</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-stone-500 uppercase tracking-wider">Jumlah (Boks)</th>
                                    <th class="px-5 py-3 text-right text-xs font-bold text-stone-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach ($riwayat->details as $detail)
                                    <tr class="hover:bg-stone-50/70 transition">
                                        <td class="px-5 py-3.5 font-semibold text-stone-800">
                                            {{-- Tampilkan nama menu jika relasi ada, fallback ke id_menu --}}
                                            {{ $detail->menu?->nama_menu ?? '(Menu #' . $detail->id_menu . ')' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-stone-600">
                                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center text-stone-700 font-semibold">
                                            {{ $detail->jumlah_boks }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold text-stone-900">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- Baris total --}}
                            <tfoot class="bg-stone-50 border-t-2 border-stone-200">
                                <tr>
                                    <td colspan="3" class="px-5 py-3 text-right text-sm font-bold text-stone-700">
                                        Total Tagihan:
                                    </td>
                                    <td class="px-5 py-3 text-right text-sm font-extrabold text-[#8B1E1E]">
                                        Rp {{ number_format($riwayat->total_tagihan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
