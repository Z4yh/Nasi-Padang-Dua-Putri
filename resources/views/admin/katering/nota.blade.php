<x-app-layout>
    <style>
        .ticket-divider {
            position: relative;
            border-top: 1.5px dashed #E5E0D8;
            margin: 1.25rem -1.25rem;
        }
        .ticket-divider::before,
        .ticket-divider::after {
            content: '';
            position: absolute;
            top: -10px;
            width: 20px;
            height: 20px;
            border-radius: 9999px;
            background-color: #FAF6F0;
            border: 1px solid #EFECE6;
        }
        .ticket-divider::before { left: -11px; }
        .ticket-divider::after { right: -11px; }

        @media print {
            nav, header, .no-print { display: none !important; }
            body, .bg-page { background: #FFFFFF !important; }
            .ticket-divider::before,
            .ticket-divider::after { background-color: #FFFFFF !important; border-color: transparent !important; }
        }
    </style>

    <div class="bg-page min-h-screen bg-[#FAF6F0] text-stone-800">
        <main class="max-w-md mx-auto px-4 py-5">

            @if (session('success'))
                <div class="no-print bg-white rounded-2xl border border-stone-200/80 shadow-xs px-4 py-3.5 mb-4 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold text-emerald-600">{{ session('success') }}</span>
                </div>
            @endif

            @foreach ($orders as $order)
                @php
                    $angka = preg_replace('/\D/', '', (string) $order->nomor_pesanan) ?: str_pad((string) $order->id_katering, 3, '0', STR_PAD_LEFT);
                    $nomorInvoice = str_starts_with((string) $order->nomor_pesanan, 'INV/')
                        ? $order->nomor_pesanan
                        : 'INV/TP-KTR/' . ($order->created_at ?? now())->format('Y/m') . '/' . $angka;

                    $ongkir = (float) ($order->ongkos_kirim ?? 0);
                    $hargaPerBoks = (float) ($order->harga_satuan ?? 0) > 0
                        ? (float) $order->harga_satuan
                        : ($order->total_boks_pesan > 0 ? round(max(0, (float) $order->total_tagihan - $ongkir) / $order->total_boks_pesan) : 0);
                    $subtotalPaket = $hargaPerBoks * (int) $order->total_boks_pesan;

                    $jam = $order->jam_kirim ? \Carbon\Carbon::parse($order->jam_kirim)->format('H.i') . ' WIB' : '';
                    $tgl = $order->tanggal_kirim ? $order->tanggal_kirim->translatedFormat('d M Y') : '-';
                    $jadwalStruk = !$order->tanggal_kirim ? '-' : ($order->tanggal_kirim->isToday() ? "Hari Ini ({$tgl}) — Pukul {$jam}" : "{$tgl} — Pukul {$jam}");

                    $waPhone = preg_replace('/\D/', '', (string) $order->no_whatsapp);
                    if (str_starts_with($waPhone, '0')) {
                        $waPhone = '62' . substr($waPhone, 1);
                    }
                    $waText = "*STRUK RESMI KATERING & BOX*\n*RM PADANG TIGA PUTRI*\nNo. Invoice: *{$nomorInvoice}*\nPemesan: *{$order->nama_pemesan}*\nJadwal: {$jadwalStruk}\nTotal: *Rp " . number_format($order->total_tagihan, 0, ',', '.') . "*\nDP: Rp " . number_format($order->nominal_dp, 0, ',', '.') . "\n*SISA TAGIHAN: Rp " . number_format($order->sisa_tagihan, 0, ',', '.') . "*";
                    $waUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($waText);
                @endphp

                <article class="bg-white rounded-3xl border border-stone-200/90 shadow-sm p-5 overflow-hidden mb-5">
                    <div class="text-center pt-1 pb-2">
                        <div class="text-stone-800 text-base mb-1.5">&#9733;</div>
                        <h1 class="text-base font-extrabold tracking-wide text-stone-900 uppercase">
                            RM PADANG TIGA PUTRI
                        </h1>
                        <p class="text-xs text-stone-600 mt-0.5">Citarasa Minang Autentik &amp; Legendaris</p>
                        <p class="text-xs text-stone-500 mt-2 leading-relaxed">
                            Jl. Raya Bumi Kahuripan Indah No. 1,<br>
                            Sukatani, Bekasi
                        </p>
                        <p class="text-xs text-stone-600 mt-1">
                            WhatsApp Dapur: <strong class="text-stone-900">0812–8877–6655</strong>
                        </p>
                        <div class="mt-3 text-[11px] font-bold tracking-wider uppercase text-stone-700">
                            STRUK RESMI KATERING &amp; BOX
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-stone-500">No. Invoice</span>
                            <span class="font-extrabold text-stone-900">{{ $nomorInvoice }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-stone-500">Waktu Pesan</span>
                            <span class="font-semibold text-stone-800">{{ ($order->created_at ?? now())->translatedFormat('d M Y • H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-stone-500">Pemesan</span>
                            <span class="font-extrabold text-stone-900">{{ $order->nama_pemesan }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-stone-500">Kontak HP</span>
                            <span class="font-medium text-stone-800">{{ $order->no_whatsapp ?: '-' }}</span>
                        </div>

                        <div class="pt-1.5 space-y-3">
                            <div>
                                <div class="text-stone-500 text-[11px]">Jadwal Pengantaran</div>
                                <div class="font-bold text-stone-900 mt-0.5">{{ $jadwalStruk }}</div>
                            </div>
                            <div>
                                <div class="text-stone-500 text-[11px]">Tujuan</div>
                                <div class="font-bold text-stone-900 mt-0.5">{{ $order->alamat_pengiriman ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="font-bold text-stone-900 text-[11px]">Catatan</div>
                                <p class="text-stone-700 mt-0.5 leading-relaxed">{{ $order->catatan_dapur ?? 'Tidak ada catatan khusus.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <div class="text-xs">
                        <h2 class="font-extrabold uppercase tracking-wider text-stone-900 mb-3">
                            RINCIAN PAKET MASAKAN
                        </h2>

                        <div class="space-y-3">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="font-semibold text-stone-800">{{ $order->nama_paket ?? 'Paket Katering Tiga Putri' }}</div>
                                    <div class="text-[11px] text-stone-500 mt-0.5">
                                        {{ $order->total_boks_pesan }} Boks &times; Rp {{ number_format($hargaPerBoks, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="font-extrabold text-stone-900 whitespace-nowrap">
                                    Rp {{ number_format($subtotalPaket, 0, ',', '.') }}
                                </div>
                            </div>

                            @if ($order->total_boks_bonus > 0)
                                <div class="flex justify-between items-center gap-3 pl-2">
                                    <div>
                                        <div class="font-bold text-stone-800">Bonus (+{{ $order->total_boks_bonus }} Boks)</div>
                                        <div class="text-[11px] text-stone-500">Gratis Boks Tambahan</div>
                                    </div>
                                    <div class="font-extrabold text-stone-900">GRATIS</div>
                                </div>
                            @endif

                            @if ($ongkir > 0)
                                <div class="flex justify-between items-center gap-3 pt-1">
                                    <span class="text-stone-600">Ongkos Kirim</span>
                                    <span class="font-semibold text-stone-800">Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center gap-3 pt-1">
                                <span class="text-stone-700 font-medium">Total Nilai Pesanan</span>
                                <span class="font-extrabold text-stone-900">Rp {{ number_format($order->total_tagihan, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-start gap-3">
                                <span class="text-stone-600 leading-snug">
                                    Uang Muka / DP Diterima<br>
                                    ({{ ucfirst($order->metode_bayar ?? 'Transfer') }})
                                </span>
                                <span class="font-bold text-stone-800 whitespace-nowrap">
                                    &ndash; Rp {{ number_format($order->nominal_dp, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center gap-3 pt-2">
                                <span class="font-extrabold uppercase text-stone-900 tracking-wide">SISA TAGIHAN</span>
                                @if ((float) $order->sisa_tagihan <= 0)
                                    <span class="font-extrabold text-sm text-emerald-600">Rp 0 (LUNAS)</span>
                                @else
                                    <span class="font-extrabold text-sm text-red-600">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <div class="text-center pt-1 pb-2 space-y-1.5">
                        <p class="italic text-xs text-stone-600">"Rasa Basamo, Lamak di Awak Katuju Dek Urang"</p>
                        <p class="text-[11px] text-stone-500 leading-relaxed">
                            Simpan bukti digital ini sebagai tanda terima sah Katering RM Tiga Putri.
                        </p>
                    </div>
                </article>

                @if ($isSingle)
                    <div class="no-print space-y-2.5 mb-8">
                        <button type="button" onclick="copyReceiptText()"
                            class="w-full py-3.5 px-4 rounded-xl bg-white hover:bg-stone-50 border border-stone-200/90 shadow-2xs text-xs font-bold text-stone-800 flex items-center justify-center gap-2 transition">
                            <span id="copyBtnLabel">Salin Struk</span>
                        </button>

                        <a href="{{ $waUrl }}" target="_blank"
                            class="w-full py-3.5 px-4 rounded-xl bg-white hover:bg-emerald-50 border border-stone-200/90 shadow-2xs text-xs font-bold text-stone-800 flex items-center justify-center gap-2 transition">
                            <span>Kirim Langsung ke WA Pelanggan</span>
                        </a>

                        <button type="button" onclick="window.print()"
                            class="w-full py-3 px-4 text-xs font-bold text-stone-700 hover:text-red-900 flex items-center justify-center gap-2 transition">
                            <span>Unduh PDF</span>
                        </button>

                        <a href="{{ route('katering.index') }}"
                            class="w-full py-2 px-4 text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center justify-center gap-1.5 transition">
                            <span>&larr; Kembali ke Buku Pesanan Katering</span>
                        </a>
                    </div>

                    <textarea id="plainReceiptText" class="hidden" readonly>{{ strip_tags($waText) }}</textarea>
                @endif
            @endforeach

            @if (!$isSingle)
                <div class="no-print space-y-2.5 mb-8">
                    <button type="button" onclick="window.print()"
                        class="w-full py-3.5 px-4 rounded-xl bg-[#8B1E1E] text-white text-xs font-bold flex items-center justify-center gap-2">
                        Cetak / Simpan Semua Rekap PDF
                    </button>
                    <a href="{{ route('katering.index') }}"
                        class="w-full py-2 px-4 text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center justify-center gap-1.5">
                        <span>&larr; Kembali ke Buku Pesanan Katering</span>
                    </a>
                </div>
            @endif
        </main>
    </div>

    <script>
        function copyReceiptText() {
            const text = document.getElementById('plainReceiptText').value;
            navigator.clipboard.writeText(text).then(() => {
                const label = document.getElementById('copyBtnLabel');
                label.innerText = 'Struk Berhasil Disalin!';
                setTimeout(() => { label.innerText = 'Salin Struk'; }, 2000);
            });
        }
    </script>
</x-layout-admin>