<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KateringOrder;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Tampilkan halaman riwayat transaksi dengan pencarian, filter, dan paginasi.
     */
    public function index(Request $request)
    {
        // Ambil semua nilai filter dari query string
        $cari    = $request->input('cari');
        $status  = $request->input('status');
        $dariBln = $request->input('dari');
        $sampai  = $request->input('sampai');

        $orders = KateringOrder::query()
            // Filter pencarian: nomor_pesanan atau nama_pemesan
            ->when($cari, function ($query, $cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('nomor_pesanan', 'like', "%{$cari}%")
                      ->orWhere('nama_pemesan', 'like', "%{$cari}%");
                });
            })
            // Filter status pesanan
            ->when($status, function ($query, $status) {
                $query->where('status_pesanan', $status);
            })
            // Filter tanggal kirim: dari tanggal
            ->when($dariBln, function ($query, $dariBln) {
                $query->whereDate('tanggal_kirim', '>=', $dariBln);
            })
            // Filter tanggal kirim: sampai tanggal
            ->when($sampai, function ($query, $sampai) {
                $query->whereDate('tanggal_kirim', '<=', $sampai);
            })
            // Urutkan dari yang terbaru
            ->latest()
            // Paginasi 10 data per halaman, withQueryString() agar filter tetap terbawa saat ganti halaman
            ->paginate(10)
            ->withQueryString();

        // Ambil daftar label status untuk dropdown filter
        $daftarStatus = KateringOrder::daftarLabelStatus();

        return view('admin.riwayat.index', compact('orders', 'daftarStatus', 'cari', 'status', 'dariBln', 'sampai'));
    }

    /**
     * Tampilkan halaman detail satu pesanan katering.
     */
    public function show(KateringOrder $riwayat)
    {
        // Eager load relasi details dan menu di setiap detail agar tidak N+1 query
        $riwayat->load('details.menu');

        // Ambil label status untuk ditampilkan di badge
        $daftarStatus = KateringOrder::daftarLabelStatus();

        return view('admin.riwayat.show', compact('riwayat', 'daftarStatus'));
    }
}
