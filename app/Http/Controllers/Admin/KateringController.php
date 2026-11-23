<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KateringOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KateringController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'semua');

        // Jika database masih kosong, tampilkan 4 kartu preview sesuai desain UI
        if (KateringOrder::count() === 0) {
            $allOrders = collect([
                new KateringOrder([
                    'id_katering' => 1,
                    'nomor_pesanan' => '#ORD-081',
                    'nama_pemesan' => 'Bpk. Hendra',
                    'nama_paket' => 'Paket Nasi Rendang Komplit',
                    'tanggal_kirim' => today()->toDateString(),
                    'jam_kirim' => '11:30:00',
                    'alamat_pengiriman' => 'Kantor Bapenda, Jl. Pemda No. 12',
                    'metode_antar' => 'Diantar via Kurir Pick-up',
                    'catatan_dapur' => 'Minta sambal ijo dipisah per bungkus & sendok plastik steril, tanpa jengkol.',
                    'total_boks_pesan' => 80,
                    'total_boks_bonus' => 2,
                    'total_tagihan' => 2240000,
                    'nominal_dp' => 1000000,
                    'sisa_tagihan' => 1240000,
                    'metode_bayar' => 'transfer',
                    'status_pesanan' => 'sedang_dimasak',
                ]),
                new KateringOrder([
                    'id_katering' => 2,
                    'nomor_pesanan' => '#ORD-082',
                    'nama_pemesan' => 'Ibu Hj. Fatimah',
                    'nama_paket' => 'Paket Nasi Ayam Pop & Gulai Tunjang',
                    'tanggal_kirim' => today()->toDateString(),
                    'jam_kirim' => '16:00:00',
                    'alamat_pengiriman' => 'Masjid Raya Baitul Makmur, Sukatanah',
                    'metode_antar' => 'Diantar',
                    'catatan_dapur' => 'Kuah gulai tunjang dibuat kental bumbu terpisah, sambal ijo tidak terlalu pedas.',
                    'total_boks_pesan' => 60,
                    'total_boks_bonus' => 2,
                    'total_tagihan' => 1680000,
                    'nominal_dp' => 0,
                    'sisa_tagihan' => 1680000,
                    'metode_bayar' => 'cod',
                    'status_pesanan' => 'menunggu_dp',
                ]),
                new KateringOrder([
                    'id_katering' => 3,
                    'nomor_pesanan' => '#ORD-080',
                    'nama_pemesan' => 'CV. Sumber Rezeki (Pak Danang)',
                    'nama_paket' => 'Paket Nasi Gulai Kakap Super',
                    'tanggal_kirim' => today()->toDateString(),
                    'jam_kirim' => '12:45:00',
                    'alamat_pengiriman' => 'Kawasan Industri Jababeka 2, Cikarang',
                    'metode_antar' => 'Diantar',
                    'info_kurir' => 'Kang Asep Pick-up Box (Plat B 4812 FOO).',
                    'catatan_dapur' => 'Nota rangkap 2 berkop Tiga Putri dibawa kurir.',
                    'total_boks_pesan' => 40,
                    'total_boks_bonus' => 0,
                    'total_tagihan' => 1800000,
                    'nominal_dp' => 1800000,
                    'sisa_tagihan' => 0,
                    'metode_bayar' => 'transfer',
                    'status_pesanan' => 'sedang_diantar',
                ]),
                new KateringOrder([
                    'id_katering' => 4,
                    'nomor_pesanan' => '#ORD-079',
                    'nama_pemesan' => 'Bpk. Rahmat',
                    'nama_paket' => 'Paket Nasi Rendang Spesial & Ayam Gulai',
                    'tanggal_kirim' => today()->subDay()->toDateString(),
                    'jam_kirim' => '12:00:00',
                    'alamat_pengiriman' => 'Perumahan Grand Cikarang Asri Blok C-12',
                    'metode_antar' => 'Terkirim & Diterima',
                    'bukti_selesai' => 'Makanan tiba pukul 11.45 WIB, diterima Ibu Rahmat, nota bertanda tangan.',
                    'total_boks_pesan' => 75,
                    'total_boks_bonus' => 2,
                    'total_tagihan' => 2100000,
                    'nominal_dp' => 2100000,
                    'sisa_tagihan' => 0,
                    'metode_bayar' => 'cod',
                    'status_pesanan' => 'selesai',
                ]),
            ]);

            $orders = match ($tab) {
                'terdekat' => $allOrders->where('status_pesanan', '!=', 'selesai')->values(),
                'selesai' => $allOrders->where('status_pesanan', 'selesai')->values(),
                default => $allOrders,
            };

            return view('admin.katering.index', [
                'orders' => $orders,
                'tab' => $tab,
                'counts' => [
                    'semua' => $allOrders->count(),
                    'terdekat' => $allOrders->where('status_pesanan', '!=', 'selesai')->count(),
                    'selesai' => $allOrders->where('status_pesanan', 'selesai')->count(),
                ],
                'todayOrderCount' => 3,
                'todayBoxCount' => 210,
                'totalReceivables' => 2450000,
            ]);
        }

        // Ambil data asli dari database jika sudah terisi
        $todayOrders = KateringOrder::whereDate('tanggal_kirim', today())
            ->where('status_pesanan', '!=', 'diarsipkan')
            ->get();

        $todayOrderCount = $todayOrders->count();
        $todayBoxCount = $todayOrders->sum(fn ($o) => (int) $o->total_boks_pesan + (int) $o->total_boks_bonus);

        $totalReceivables = KateringOrder::where('status_pesanan', '!=', 'diarsipkan')
            ->where('sisa_tagihan', '>', 0)
            ->sum('sisa_tagihan');

        $counts = [
            'semua' => KateringOrder::where('status_pesanan', '!=', 'diarsipkan')->count(),
            'terdekat' => KateringOrder::whereNotIn('status_pesanan', ['selesai', 'diarsipkan'])->count(),
            'selesai' => KateringOrder::where('status_pesanan', 'selesai')->count(),
        ];

        $query = KateringOrder::query()->where('status_pesanan', '!=', 'diarsipkan');

        if ($tab === 'terdekat') {
            $query->where('status_pesanan', '!=', 'selesai')->orderBy('tanggal_kirim')->orderBy('jam_kirim');
        } elseif ($tab === 'selesai') {
            $query->where('status_pesanan', 'selesai')->orderByDesc('tanggal_kirim');
        } else {
            $query->orderByRaw("CASE WHEN status_pesanan = 'selesai' THEN 1 ELSE 0 END ASC")->orderBy('tanggal_kirim');
        }

        $orders = $query->get();

        return view('admin.katering.index', compact(
            'orders', 'tab', 'counts', 'todayOrderCount', 'todayBoxCount', 'totalReceivables'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_whatsapp' => 'nullable|string|max:30',
            'nama_paket' => 'required|string|max:255',
            'total_boks_pesan' => 'required|integer|min:1',
            'total_boks_bonus' => 'nullable|integer|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'ongkos_kirim' => 'nullable|numeric|min:0',
            'tanggal_kirim' => 'required|date',
            'jam_kirim' => 'required',
            'alamat_pengiriman' => 'required|string',
            'metode_antar' => 'required|string|max:100',
            'info_kurir' => 'nullable|string|max:255',
            'catatan_dapur' => 'nullable|string',
            'total_tagihan' => 'required|numeric|min:0',
            'nominal_dp' => 'nullable|numeric|min:0',
            'metode_bayar' => 'required|in:transfer,cod',
            'bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $totalTagihan = (float) $validated['total_tagihan'];
        $nominalDp = (float) ($validated['nominal_dp'] ?? 0);
        $sisaTagihan = max(0, $totalTagihan - $nominalDp);
        $statusPesanan = $nominalDp > 0 ? 'sedang_dimasak' : 'menunggu_dp';

        $lastCode = KateringOrder::orderByDesc('id_katering')->value('nomor_pesanan');
        $lastNumber = $lastCode ? (int) preg_replace('/\D/', '', $lastCode) : 83;
        $nomorPesanan = '#ORD-' . str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);

        $buktiPath = $request->hasFile('bukti_bayar')
            ? $request->file('bukti_bayar')->store('bukti-katering', 'public')
            : null;

        $data = [
            'nomor_pesanan' => $nomorPesanan,
            'id_user' => auth()->id(),
            'nama_pemesan' => $validated['nama_pemesan'],
            'no_whatsapp' => $validated['no_whatsapp'] ?? '-',
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'jam_kirim' => $validated['jam_kirim'],
            'total_boks_pesan' => $validated['total_boks_pesan'],
            'total_boks_bonus' => $validated['total_boks_bonus'] ?? 0,
            'total_tagihan' => $totalTagihan,
            'nominal_dp' => $nominalDp,
            'sisa_tagihan' => $sisaTagihan,
            'bukti_bayar' => $buktiPath,
            'status_pesanan' => $statusPesanan,
        ];

        $optionalCols = [
            'nama_paket' => $validated['nama_paket'],
            'alamat_pengiriman' => $validated['alamat_pengiriman'],
            'metode_antar' => $validated['metode_antar'],
            'info_kurir' => $validated['info_kurir'] ?? null,
            'catatan_dapur' => $validated['catatan_dapur'] ?? null,
            'harga_satuan' => $validated['harga_satuan'] ?? 0,
            'ongkos_kirim' => $validated['ongkos_kirim'] ?? 0,
            'metode_bayar' => $validated['metode_bayar'],
        ];

        foreach ($optionalCols as $col => $val) {
            if (Schema::hasColumn('tigaputri_md_katering_order', $col)) {
                $data[$col] = $val;
            }
        }

        $order = KateringOrder::create($data);

        return redirect()->route('katering.nota', $order)
            ->with('success', 'Pesanan Berhasil Disimpan!');
    }

    public function update(Request $request, KateringOrder $kateringOrder): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_whatsapp' => 'nullable|string|max:30',
            'nama_paket' => 'required|string|max:255',
            'total_boks_pesan' => 'required|integer|min:1',
            'total_boks_bonus' => 'nullable|integer|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'ongkos_kirim' => 'nullable|numeric|min:0',
            'tanggal_kirim' => 'required|date',
            'jam_kirim' => 'required',
            'alamat_pengiriman' => 'required|string',
            'metode_antar' => 'required|string|max:100',
            'info_kurir' => 'nullable|string|max:255',
            'catatan_dapur' => 'nullable|string',
            'total_tagihan' => 'required|numeric|min:0',
            'nominal_dp' => 'nullable|numeric|min:0',
            'metode_bayar' => 'required|in:transfer,cod',
            'bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $totalTagihan = (float) $validated['total_tagihan'];
        $nominalDp = (float) ($validated['nominal_dp'] ?? 0);
        $sisaTagihan = max(0, $totalTagihan - $nominalDp);

        $buktiPath = $request->hasFile('bukti_bayar')
            ? $request->file('bukti_bayar')->store('bukti-katering', 'public')
            : $kateringOrder->bukti_bayar;

        $data = [
            'nama_pemesan' => $validated['nama_pemesan'],
            'no_whatsapp' => $validated['no_whatsapp'] ?? $kateringOrder->no_whatsapp,
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'jam_kirim' => $validated['jam_kirim'],
            'total_boks_pesan' => $validated['total_boks_pesan'],
            'total_boks_bonus' => $validated['total_boks_bonus'] ?? 0,
            'total_tagihan' => $totalTagihan,
            'nominal_dp' => $nominalDp,
            'sisa_tagihan' => $sisaTagihan,
            'bukti_bayar' => $buktiPath,
        ];

        $optionalCols = [
            'nama_paket' => $validated['nama_paket'],
            'alamat_pengiriman' => $validated['alamat_pengiriman'],
            'metode_antar' => $validated['metode_antar'],
            'info_kurir' => $validated['info_kurir'] ?? null,
            'catatan_dapur' => $validated['catatan_dapur'] ?? null,
            'harga_satuan' => $validated['harga_satuan'] ?? 0,
            'ongkos_kirim' => $validated['ongkos_kirim'] ?? 0,
            'metode_bayar' => $validated['metode_bayar'],
        ];

        foreach ($optionalCols as $col => $val) {
            if (Schema::hasColumn('tigaputri_md_katering_order', $col)) {
                $data[$col] = $val;
            }
        }

        $kateringOrder->update($data);

        return redirect()->route('katering.index')
            ->with('success', "Data pesanan {$kateringOrder->nomor_pesanan} berhasil diperbarui.");
    }

    public function updateStatus(Request $request, KateringOrder $kateringOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status_pesanan' => 'required|in:menunggu_dp,sedang_dimasak,sedang_diantar,selesai',
        ]);

        $kateringOrder->update([
            'status_pesanan' => $validated['status_pesanan'],
        ]);

        return back()->with('success', "Status {$kateringOrder->nomor_pesanan} diperbarui.");
    }

    public function confirmPayment(Request $request, KateringOrder $kateringOrder): RedirectResponse
    {
        if ($request->input('action') === 'lunas') {
            $kateringOrder->update([
                'nominal_dp' => $kateringOrder->total_tagihan,
                'sisa_tagihan' => 0,
            ]);

            return back()->with('success', "Tagihan {$kateringOrder->nomor_pesanan} ditandai LUNAS.");
        }

        $validated = $request->validate([
            'nominal_dp' => 'required|numeric|min:1',
            'metode_bayar' => 'required|in:transfer,cod',
        ]);

        $nominalDp = (float) $validated['nominal_dp'];
        $sisaTagihan = max(0, (float) $kateringOrder->total_tagihan - $nominalDp);

        $updateData = [
            'nominal_dp' => $nominalDp,
            'sisa_tagihan' => $sisaTagihan,
            'status_pesanan' => $kateringOrder->status_pesanan === 'menunggu_dp' ? 'sedang_dimasak' : $kateringOrder->status_pesanan,
        ];

        if (Schema::hasColumn('tigaputri_md_katering_order', 'metode_bayar')) {
            $updateData['metode_bayar'] = $validated['metode_bayar'];
        }

        $kateringOrder->update($updateData);

        return back()->with('success', "DP untuk {$kateringOrder->nomor_pesanan} berhasil dikonfirmasi.");
    }

    public function markCompleted(Request $request, KateringOrder $kateringOrder): RedirectResponse
    {
        $validated = $request->validate([
            'bukti_selesai' => 'required|string|max:500',
        ]);

        $updateData = [
            'status_pesanan' => 'selesai',
            'nominal_dp' => $kateringOrder->total_tagihan,
            'sisa_tagihan' => 0,
        ];

        if (Schema::hasColumn('tigaputri_md_katering_order', 'metode_antar')) {
            $updateData['metode_antar'] = 'Terkirim & Diterima';
        }
        if (Schema::hasColumn('tigaputri_md_katering_order', 'bukti_selesai')) {
            $updateData['bukti_selesai'] = $validated['bukti_selesai'];
        }

        $kateringOrder->update($updateData);

        return back()->with('success', "Pesanan {$kateringOrder->nomor_pesanan} telah selesai & tuntas.");
    }

    public function archive(KateringOrder $kateringOrder): RedirectResponse
    {
        $kateringOrder->update([
            'status_pesanan' => 'diarsipkan',
        ]);

        return back()->with('success', "Pesanan {$kateringOrder->nomor_pesanan} dipindahkan ke arsip.");
    }

    public function nota(KateringOrder $kateringOrder): View
    {
        $orders = collect([$kateringOrder]);
        $isSingle = true;

        return view('admin.katering.nota', compact('orders', 'isSingle'));
    }

    public function exportPdf(): View
    {
        $orders = KateringOrder::where('status_pesanan', '!=', 'diarsipkan')
            ->orderBy('tanggal_kirim', 'asc')
            ->orderBy('jam_kirim', 'asc')
            ->get();
        $isSingle = false;

        return view('admin.katering.nota', compact('orders', 'isSingle'));
    }

    public function exportCsv(): StreamedResponse
    {
        $orders = KateringOrder::where('status_pesanan', '!=', 'diarsipkan')
            ->orderBy('tanggal_kirim', 'asc')
            ->orderBy('jam_kirim', 'asc')
            ->get();

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Nomor Pesanan',
                'Tanggal Kirim',
                'Jam Kirim',
                'Nama Pemesan',
                'No WhatsApp',
                'Paket Katering',
                'Boks Pesan',
                'Boks Bonus',
                'Total Masak Fisik',
                'Total Tagihan',
                'Nominal DP',
                'Sisa Tagihan',
                'Status Pesanan',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->nomor_pesanan,
                    $order->tanggal_kirim?->format('Y-m-d'),
                    $order->jam_kirim,
                    $order->nama_pemesan,
                    $order->no_whatsapp,
                    $order->nama_paket ?? 'Paket Katering Tiga Putri',
                    $order->total_boks_pesan,
                    $order->total_boks_bonus,
                    (int) $order->total_boks_pesan + (int) $order->total_boks_bonus,
                    $order->total_tagihan,
                    $order->nominal_dp,
                    $order->sisa_tagihan,
                    strtoupper(str_replace('_', ' ', $order->status_pesanan)),
                ]);
            }

            fclose($handle);
        }, 'rekap-pesanan-katering-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}