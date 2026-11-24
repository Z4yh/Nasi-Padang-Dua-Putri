<?php

namespace Database\Seeders;

use App\Models\DetailTransaksiKasir;
use App\Models\KategoriPengeluaran;
use App\Models\KateringDetail;
use App\Models\KateringOrder;
use App\Models\Menu;
use App\Models\Pengeluaran;
use App\Models\SettingKatering;
use App\Models\TransaksiKasir;
use App\Models\TutupKasir;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil referensi User dan Menu yang sudah ada
        $penjual = User::where('role', 'penjual')->first() ?? User::where('email', 'penjual@tigaputri.com')->first();
        $superAdmin = User::where('role', 'super_admin')->first() ?? User::where('email', 'superadmin@tigaputri.com')->first();

        $menus = Menu::all();
        if ($menus->isEmpty()) {
            $this->command?->warn('Tabel menu kosong, seeder dummy data dilewati.');
            return;
        }

        $setting = SettingKatering::first();
        $minOrderBonus = $setting?->min_order_bonus ?? 20;
        $bonusBox = $setting?->jumlah_bonus_box ?? 1;
        $minDpPersen = $setting?->min_dp_persen ?? 50;

        $now = now();

        // 2. Kategori Pengeluaran (Idempoten: lewati jika sudah ada)
        $kategoriList = [
            ['nama_kategori_pengeluaran' => 'Belanja Bahan Baku & Daging', 'status_aktif' => 1],
            ['nama_kategori_pengeluaran' => 'Sayur-Mayur & Bumbu Dapur', 'status_aktif' => 1],
            ['nama_kategori_pengeluaran' => 'Gas LPG & Operasional Dapur', 'status_aktif' => 1],
            ['nama_kategori_pengeluaran' => 'Listrik, Air & Kebersihan', 'status_aktif' => 1],
            ['nama_kategori_pengeluaran' => 'Gaji & Uang Makan Karyawan', 'status_aktif' => 1],
        ];

        foreach ($kategoriList as $kat) {
            if (!KategoriPengeluaran::where('nama_kategori_pengeluaran', $kat['nama_kategori_pengeluaran'])->exists()) {
                KategoriPengeluaran::create($kat);
            }
        }
        $kategoriModels = KategoriPengeluaran::all();

        // 3. Transaksi Kasir & Detail (40 Data Transaksi, tersebar 60 hari ke belakang)
        // Distribusi hari: 0 (hari ini: 3 transaksi), 1-6 (minggu ini: 14 transaksi), 7-58 (23 transaksi) = total 40 transaksi
        $hariOffsetKasir = [
            0, 0, 0,
            1, 1, 1,
            2, 2,
            3, 3, 3,
            4, 4,
            5, 5,
            6, 6,
            8, 10, 12, 14, 16, 18, 20, 22, 25, 27, 30, 32, 35, 38, 40, 42, 45, 48, 50, 52, 55, 57, 59,
        ];

        $metodeList = ['tunai', 'tunai', 'qris', 'tunai', 'transfer', 'tunai', 'qris'];

        for ($i = 0; $i < count($hariOffsetKasir); $i++) {
            $offset = $hariOffsetKasir[$i];
            $transDate = $now->copy()->subDays($offset);
            // Waktu jam acak realistis antara jam 10:00 sampai 21:00
            $jam = 10 + ($i % 11);
            $menit = ($i * 17) % 60;
            $detik = ($i * 23) % 60;
            $transDateTime = $transDate->copy()->setTime($jam, $menit, $detik);

            $nomorTransaksi = sprintf('TP-%s-%04d', $transDateTime->format('Ymd'), $i + 1);

            if (TransaksiKasir::where('nomor_transaksi', $nomorTransaksi)->exists()) {
                continue;
            }

            // Pilih 1 sampai 3 menu untuk detail transaksi
            $jumlahItem = 1 + ($i % 3);
            $selectedMenus = $menus->shuffle()->take($jumlahItem);

            $subtotalTransaksi = 0;
            $detailPayloads = [];

            foreach ($selectedMenus as $idx => $m) {
                $qty = 1 + (($i + $idx) % 3); // 1, 2, atau 3 porsi
                $hargaSatuan = (float) $m->harga;
                $subtotalItem = $qty * $hargaSatuan;

                $subtotalTransaksi += $subtotalItem;
                $detailPayloads[] = [
                    'id_menu'       => $m->id_menu,
                    'jumlah'        => $qty,
                    'harga_satuan'  => $hargaSatuan,
                    'subtotal_item' => $subtotalItem,
                ];
            }

            $metode = $metodeList[$i % count($metodeList)];
            $totalBayar = $subtotalTransaksi;
            $kembalian = 0;

            if ($metode === 'tunai') {
                // Pembayaran tunai dibulatkan ke kelipatan Rp 10.000 atau Rp 50.000 terdekat ke atas
                $totalBayar = (int) ceil($subtotalTransaksi / 10000) * 10000;
                if ($totalBayar == $subtotalTransaksi) {
                    $totalBayar += 10000;
                }
                $kembalian = $totalBayar - $subtotalTransaksi;
            }

            $transaksi = TransaksiKasir::create([
                'nomor_transaksi'   => $nomorTransaksi,
                'id_user'           => $penjual?->id_user ?? 2,
                'metode_pembayaran' => $metode,
                'subtotal'          => $subtotalTransaksi,
                'total_bayar'       => $totalBayar,
                'kembalian'         => $kembalian,
                'created_at'        => $transDateTime,
                'updated_at'        => $transDateTime,
            ]);

            foreach ($detailPayloads as $payload) {
                if (!DetailTransaksiKasir::where('id_transaksi', $transaksi->id_transaksi)->where('id_menu', $payload['id_menu'])->exists()) {
                    DetailTransaksiKasir::create([
                        'id_transaksi'  => $transaksi->id_transaksi,
                        'id_menu'       => $payload['id_menu'],
                        'jumlah'        => $payload['jumlah'],
                        'harga_satuan'  => $payload['harga_satuan'],
                        'subtotal_item' => $payload['subtotal_item'],
                        'created_at'    => $transDateTime,
                        'updated_at'    => $transDateTime,
                    ]);
                }
            }
        }

        // 4. Katering Order & Detail (30 Data Pesanan)
        // Komposisi status: 12 selesai, 5 menunggu_dp, 6 diproses, 4 siap_kirim, 3 dibatalkan = 30 pesanan
        $daftarStatusKatering = [
            // 12 Selesai (tanggal_kirim di masa lalu, created_at sebelum tanggal_kirim, lunas)
            ['status' => 'selesai', 'offset_kirim' => -50, 'offset_create' => -53],
            ['status' => 'selesai', 'offset_kirim' => -45, 'offset_create' => -48],
            ['status' => 'selesai', 'offset_kirim' => -40, 'offset_create' => -43],
            ['status' => 'selesai', 'offset_kirim' => -35, 'offset_create' => -38],
            ['status' => 'selesai', 'offset_kirim' => -30, 'offset_create' => -33],
            ['status' => 'selesai', 'offset_kirim' => -25, 'offset_create' => -28],
            ['status' => 'selesai', 'offset_kirim' => -20, 'offset_create' => -23],
            ['status' => 'selesai', 'offset_kirim' => -15, 'offset_create' => -18],
            ['status' => 'selesai', 'offset_kirim' => -10, 'offset_create' => -13],
            ['status' => 'selesai', 'offset_kirim' => -6,  'offset_create' => -9],
            ['status' => 'selesai', 'offset_kirim' => -3,  'offset_create' => -6],
            ['status' => 'selesai', 'offset_kirim' => -1,  'offset_create' => -4],

            // 5 Menunggu DP (tanggal_kirim hari ini s/d 7 hari ke depan, DP 0, sisa = tagihan)
            ['status' => 'menunggu_dp', 'offset_kirim' => 1, 'offset_create' => -1],
            ['status' => 'menunggu_dp', 'offset_kirim' => 2, 'offset_create' => 0],
            ['status' => 'menunggu_dp', 'offset_kirim' => 3, 'offset_create' => 0],
            ['status' => 'menunggu_dp', 'offset_kirim' => 5, 'offset_create' => -1],
            ['status' => 'menunggu_dp', 'offset_kirim' => 7, 'offset_create' => -2],

            // 6 Diproses (tanggal_kirim hari ini s/d 7 hari ke depan, DP minimal 50%)
            ['status' => 'diproses', 'offset_kirim' => 1, 'offset_create' => -2],
            ['status' => 'diproses', 'offset_kirim' => 2, 'offset_create' => -1],
            ['status' => 'diproses', 'offset_kirim' => 3, 'offset_create' => -2],
            ['status' => 'diproses', 'offset_kirim' => 4, 'offset_create' => -3],
            ['status' => 'diproses', 'offset_kirim' => 6, 'offset_create' => -2],
            ['status' => 'diproses', 'offset_kirim' => 7, 'offset_create' => -3],

            // 4 Siap Kirim (tanggal_kirim hari ini s/d 7 hari ke depan, DP minimal 50%)
            ['status' => 'siap_kirim', 'offset_kirim' => 0, 'offset_create' => -3],
            ['status' => 'siap_kirim', 'offset_kirim' => 1, 'offset_create' => -2],
            ['status' => 'siap_kirim', 'offset_kirim' => 2, 'offset_create' => -3],
            ['status' => 'siap_kirim', 'offset_kirim' => 3, 'offset_create' => -2],

            // 3 Dibatalkan (created_at sebelum tanggal_kirim, DP 0, sisa = tagihan)
            ['status' => 'dibatalkan', 'offset_kirim' => -12, 'offset_create' => -15],
            ['status' => 'dibatalkan', 'offset_kirim' => -5,  'offset_create' => -8],
            ['status' => 'dibatalkan', 'offset_kirim' => 4,   'offset_create' => -1],
        ];

        $namaPemesanList = [
            'Hendra Kurniawan', 'Dewi Sartika', 'Rahmat Hidayat', 'Siti Nurhaliza', 'Bambang Pamungkas',
            'Fitri Handayani', 'Agus Setiawan', 'Maya Anggraini', 'Dedi Suhendar', 'Ratna Juwita',
            'Eko Prasetyo', 'Nurul Hasanah', 'Rizky Ramadhan', 'Sri Wahyuni', 'Fajar Pratama',
            'Lestari Pujiastuti', 'Aris Munandar', 'Yuliana Sari', 'Budi Hermanto', 'Intan Permatasari',
            'Gunawan Wibisono', 'Mega Utami', 'Doni Iskandar', 'Tia Rahmawati', 'Ilham Saputra',
            'Wulan Dari', 'Ari Wibowo', 'Putri Amelia', 'Bayu Segara', 'Anisa Septiani',
        ];

        $jamKirimList = ['10:00:00', '11:00:00', '11:30:00', '12:00:00', '15:30:00', '17:00:00'];

        for ($k = 0; $k < count($daftarStatusKatering); $k++) {
            $conf = $daftarStatusKatering[$k];
            $tglKirim = $now->copy()->addDays($conf['offset_kirim'])->toDateString();
            $createdAt = $now->copy()->addDays($conf['offset_create'])->setTime(9 + ($k % 8), ($k * 13) % 60, 0);

            $nomorPesanan = sprintf('INV/TP-KTR/%s/%s/%03d', $createdAt->format('Y'), $createdAt->format('m'), $k + 1);

            if (KateringOrder::where('nomor_pesanan', $nomorPesanan)->exists()) {
                continue;
            }

            // Tentukan isi boks katering (1 atau 2 macam menu)
            // Variasi jumlah boks: misal 15, 20, 25, 30, 40, 50 boks
            $boxVariants = [15, 20, 25, 30, 35, 40, 50, 60];
            $totalBoksPesan = $boxVariants[$k % count($boxVariants)];

            // Hitung bonus boks: jika total_boks_pesan >= min_order_bonus, bonus = jumlah_bonus_box (sekali, bukan kelipatan)
            $totalBoksBonus = ($totalBoksPesan >= $minOrderBonus) ? $bonusBox : 0;

            // Rincian menu detail
            $menuPilihan = $menus->shuffle()->take(2);
            $detailKateringPayloads = [];
            $totalTagihan = 0;

            if ($menuPilihan->count() === 1 || ($k % 3 === 0)) {
                // 1 menu untuk semua boks
                $m = $menuPilihan->first();
                $harga = (float) $m->harga;
                $subtotal = $totalBoksPesan * $harga;
                $totalTagihan += $subtotal;

                $detailKateringPayloads[] = [
                    'id_menu'      => $m->id_menu,
                    'jumlah_boks'  => $totalBoksPesan,
                    'harga_satuan' => $harga,
                    'subtotal'     => $subtotal,
                ];
            } else {
                // Split boks ke 2 menu (misal separuh-separuh)
                $m1 = $menuPilihan[0];
                $m2 = $menuPilihan[1];
                $boks1 = (int) floor($totalBoksPesan / 2);
                $boks2 = $totalBoksPesan - $boks1;

                $sub1 = $boks1 * (float) $m1->harga;
                $sub2 = $boks2 * (float) $m2->harga;

                $totalTagihan += ($sub1 + $sub2);

                $detailKateringPayloads[] = [
                    'id_menu'      => $m1->id_menu,
                    'jumlah_boks'  => $boks1,
                    'harga_satuan' => (float) $m1->harga,
                    'subtotal'     => $sub1,
                ];
                $detailKateringPayloads[] = [
                    'id_menu'      => $m2->id_menu,
                    'jumlah_boks'  => $boks2,
                    'harga_satuan' => (float) $m2->harga,
                    'subtotal'     => $sub2,
                ];
            }

            // Hitung DP dan sisa tagihan sesuai status
            $status = $conf['status'];
            $nominalDp = 0;
            $sisaTagihan = $totalTagihan;
            $buktiBayar = null;

            if ($status === 'selesai') {
                $nominalDp = $totalTagihan;
                $sisaTagihan = 0;
                $buktiBayar = 'bukti_bayar/sample_lunas.jpg';
            } elseif (in_array($status, ['diproses', 'siap_kirim'])) {
                // DP minimal min_dp_persen (50%)
                $nominalDp = round($totalTagihan * ($minDpPersen / 100));
                $sisaTagihan = $totalTagihan - $nominalDp;
                $buktiBayar = 'bukti_bayar/sample_dp.jpg';
            } elseif ($status === 'menunggu_dp' || $status === 'dibatalkan') {
                $nominalDp = 0;
                $sisaTagihan = $totalTagihan;
                $buktiBayar = null;
            }

            $order = KateringOrder::create([
                'nomor_pesanan'    => $nomorPesanan,
                'id_user'          => null,
                'nama_pemesan'     => $namaPemesanList[$k % count($namaPemesanList)],
                'no_whatsapp'      => '08' . sprintf('12%08d', ($k * 9876543) % 100000000),
                'tanggal_kirim'    => $tglKirim,
                'jam_kirim'        => $jamKirimList[$k % count($jamKirimList)],
                'total_boks_pesan' => $totalBoksPesan,
                'total_boks_bonus' => $totalBoksBonus,
                'total_tagihan'    => $totalTagihan,
                'nominal_dp'       => $nominalDp,
                'sisa_tagihan'     => $sisaTagihan,
                'bukti_bayar'      => $buktiBayar,
                'status_pesanan'   => $status,
                'created_at'       => $createdAt,
                'updated_at'       => $createdAt,
            ]);

            foreach ($detailKateringPayloads as $payload) {
                if (!KateringDetail::where('id_katering', $order->id_katering)->where('id_menu', $payload['id_menu'])->exists()) {
                    KateringDetail::create([
                        'id_katering'  => $order->id_katering,
                        'id_menu'      => $payload['id_menu'],
                        'jumlah_boks'  => $payload['jumlah_boks'],
                        'harga_satuan' => $payload['harga_satuan'],
                        'subtotal'     => $payload['subtotal'],
                        'created_at'   => $createdAt,
                        'updated_at'   => $createdAt,
                    ]);
                }
            }
        }

        // 5. Pengeluaran (15 Data, tersebar 60 hari ke belakang, termasuk hari ini dan minggu ini)
        $daftarPengeluaran = [
            ['offset' => 0,  'nominal' => 350000,  'sumber' => 'kas_laci',      'kat_idx' => 0, 'ket' => 'Belanja daging sapi segar 3.5 kg ke pasar'],
            ['offset' => 0,  'nominal' => 85000,   'sumber' => 'kas_laci',      'kat_idx' => 1, 'ket' => 'Bumbu dapur, cabai merah, dan kelapa parut'],
            ['offset' => 1,  'nominal' => 220000,  'sumber' => 'kas_laci',      'kat_idx' => 2, 'ket' => 'Isi ulang gas LPG 3kg 10 tabung'],
            ['offset' => 2,  'nominal' => 450000,  'sumber' => 'rekening_bank', 'kat_idx' => 0, 'ket' => 'Beli beras Solok premium 2 karung @25kg'],
            ['offset' => 4,  'nominal' => 150000,  'sumber' => 'kas_laci',      'kat_idx' => 4, 'ket' => 'Uang makan & lembur karyawan katering'],
            ['offset' => 6,  'nominal' => 120000,  'sumber' => 'kas_laci',      'kat_idx' => 3, 'ket' => 'Perlengkapan sabun cuci piring & plastik kresek'],
            ['offset' => 10, 'nominal' => 750000,  'sumber' => 'rekening_bank', 'kat_idx' => 3, 'ket' => 'Pembayaran tagihan listrik PLN bulanan'],
            ['offset' => 15, 'nominal' => 500000,  'sumber' => 'kas_laci',      'kat_idx' => 0, 'ket' => 'Belanja ayam potong 20 ekor untuk stok'],
            ['offset' => 20, 'nominal' => 210000,  'sumber' => 'kas_laci',      'kat_idx' => 2, 'ket' => 'Isi ulang gas LPG dapur'],
            ['offset' => 28, 'nominal' => 95000,   'sumber' => 'kas_laci',      'kat_idx' => 1, 'ket' => 'Bumbu rempah kering, kapulaga & cengkeh'],
            ['offset' => 35, 'nominal' => 1200000, 'sumber' => 'rekening_bank', 'kat_idx' => 4, 'ket' => 'Gaji mingguan juru masak kepala'],
            ['offset' => 42, 'nominal' => 600000,  'sumber' => 'rekening_bank', 'kat_idx' => 0, 'ket' => 'Beli kikil tunjang dan jeroan sapi segar'],
            ['offset' => 48, 'nominal' => 180000,  'sumber' => 'pribadi',       'kat_idx' => 3, 'ket' => 'Servis kompor gas mawar tekanan tinggi'],
            ['offset' => 54, 'nominal' => 320000,  'sumber' => 'kas_laci',      'kat_idx' => 1, 'ket' => 'Sayuran daun singkong dan nangka muda'],
            ['offset' => 58, 'nominal' => 400000,  'sumber' => 'rekening_bank', 'kat_idx' => 0, 'ket' => 'Beras Solok 2 karung'],
        ];

        foreach ($daftarPengeluaran as $p) {
            $tglPengeluaran = $now->copy()->subDays($p['offset'])->toDateString();
            $kategoriId = $kategoriModels[$p['kat_idx'] % $kategoriModels->count()]->id_kategori_pengeluaran;

            if (Pengeluaran::where('tanggal_pengeluaran', $tglPengeluaran)->where('keterangan', $p['ket'])->exists()) {
                continue;
            }

            Pengeluaran::create([
                'id_user'                 => $superAdmin?->id_user ?? 1,
                'id_kategori_pengeluaran' => $kategoriId,
                'tanggal_pengeluaran'     => $tglPengeluaran,
                'nominal'                 => $p['nominal'],
                'sumber_dana'             => $p['sumber'],
                'keterangan'              => $p['ket'],
                'created_at'              => $now->copy()->subDays($p['offset'])->setTime(8, 0, 0),
                'updated_at'              => $now->copy()->subDays($p['offset'])->setTime(8, 0, 0),
            ]);
        }

        // 6. Tutup Kasir (Beberapa hari terakhir: 6 hari ke belakang hingga hari ini)
        // Aturan: selisih = total_kas_fisik dikurangi total transaksi tunai hari itu.
        // total_mutasi_qris = total qris hari itu.
        $tutupKasirVariasiSelisih = [
            0 => 0,      // Hari ini: pas
            1 => -2000,  // Kemarin: selisih -2000
            2 => 5000,   // 2 hari lalu: selisih +5000
            3 => 0,      // 3 hari lalu: pas
            4 => -5000,  // 4 hari lalu: selisih -5000
            5 => 3000,   // 5 hari lalu: selisih +3000
            6 => 0,      // 6 hari lalu: pas
        ];

        foreach ($tutupKasirVariasiSelisih as $offsetHari => $variasiSelisih) {
            $tglTutup = $now->copy()->subDays($offsetHari)->toDateString();
            $penjualId = $penjual?->id_user ?? 2;

            if (TutupKasir::where('tanggal_tutup', $tglTutup)->where('id_user', $penjualId)->exists()) {
                continue;
            }

            // Hitung total transaksi tunai dan QRIS hari itu
            $totalTunaiHariItu = (float) TransaksiKasir::whereDate('created_at', $tglTutup)
                ->where('metode_pembayaran', 'tunai')
                ->sum('subtotal');

            $totalQrisHariItu = (float) TransaksiKasir::whereDate('created_at', $tglTutup)
                ->where('metode_pembayaran', 'qris')
                ->sum('subtotal');

            // Jika ada transaksi tunai, terapkan variasi selisih; jika 0, selisih 0
            $selisih = ($totalTunaiHariItu > 0) ? $variasiSelisih : 0;
            $totalKasFisik = max(0, $totalTunaiHariItu + $selisih);
            // Sesuai rumus: selisih = total_kas_fisik - total_transaksi_tunai
            $selisih = $totalKasFisik - $totalTunaiHariItu;

            TutupKasir::create([
                'id_user'           => $penjualId,
                'tanggal_tutup'     => $tglTutup,
                'total_kas_fisik'   => $totalKasFisik,
                'total_mutasi_qris' => $totalQrisHariItu,
                'selisih'           => $selisih,
                'created_at'        => $now->copy()->subDays($offsetHari)->setTime(21, 30, 0),
                'updated_at'        => $now->copy()->subDays($offsetHari)->setTime(21, 30, 0),
            ]);
        }
    }
}
