<?php

namespace Tests\Feature;

use App\Models\CmsBeranda;
use App\Models\CmsMenuFavorit;
use App\Models\DetailTransaksiKasir;
use App\Models\EmailOtp;
use App\Models\KategoriMenu;
use App\Models\KategoriPengeluaran;
use App\Models\KateringDetail;
use App\Models\KateringOrder;
use App\Models\Menu;
use App\Models\Pengeluaran;
use App\Models\SettingKatering;
use App\Models\TransaksiKasir;
use App\Models\TutupKasir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemaAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_14_models_and_their_relations(): void
    {
        // 1. User
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);
        $this->assertDatabaseHas('tigaputri_md_users', [
            'id_user' => $user->id_user,
            'role'    => 'super_admin',
        ]);

        // 2. EmailOtp
        $otp = EmailOtp::create([
            'id_user'          => $user->id_user,
            'kode_otp'         => '123456',
            'waktu_kadaluarsa' => now()->addMinutes(15),
        ]);
        $this->assertEquals($user->id_user, $otp->user->id_user);
        $this->assertTrue($user->emailOtps->contains($otp));

        // 3. KategoriMenu
        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Lauk Minang',
            'slug_kategori' => 'lauk-minang',
            'urutan'        => 1,
        ]);
        $this->assertDatabaseHas('tigaputri_md_kategori_menu', [
            'id_kategori_menu' => $kategori->id_kategori_menu,
        ]);

        // 4. Menu
        $menu = Menu::create([
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Gulai Cumi Isi',
            'slug_menu'        => 'gulai-cumi-isi',
            'harga'            => 35000,
            'status_stok'      => 'tersedia',
        ]);
        $this->assertEquals($kategori->id_kategori_menu, $menu->kategori->id_kategori_menu);
        $this->assertTrue($kategori->menu->contains($menu));

        // 5. TransaksiKasir
        $transaksi = TransaksiKasir::create([
            'nomor_transaksi'   => 'TRX-2026-0001',
            'id_user'           => $user->id_user,
            'metode_pembayaran' => 'tunai',
            'subtotal'          => 70000,
            'total_bayar'       => 100000,
            'kembalian'         => 30000,
        ]);
        $this->assertEquals($user->id_user, $transaksi->user->id_user);
        $this->assertTrue($user->transaksiKasir->contains($transaksi));

        // 6. DetailTransaksiKasir
        $detailTrx = DetailTransaksiKasir::create([
            'id_transaksi'  => $transaksi->id_transaksi,
            'id_menu'       => $menu->id_menu,
            'jumlah'        => 2,
            'harga_satuan'  => 35000,
            'subtotal_item' => 70000,
        ]);
        $this->assertEquals($transaksi->id_transaksi, $detailTrx->transaksi->id_transaksi);
        $this->assertEquals($menu->id_menu, $detailTrx->menu->id_menu);

        // 7. TutupKasir
        $tutup = TutupKasir::create([
            'id_user'           => $user->id_user,
            'tanggal_tutup'     => now()->toDateString(),
            'total_kas_fisik'   => 500000,
            'total_mutasi_qris' => 300000,
            'selisih'           => 0,
        ]);
        $this->assertEquals($user->id_user, $tutup->user->id_user);
        $this->assertTrue($user->tutupKasir->contains($tutup));

        // 8. SettingKatering
        $setting = SettingKatering::create([
            'min_order_bonus'  => 25,
            'jumlah_bonus_box' => 2,
            'min_dp_persen'    => 50,
        ]);
        $this->assertDatabaseHas('tigaputri_md_setting_katering', [
            'id_setting_katering' => $setting->id_setting_katering,
            'min_order_bonus'     => 25,
        ]);

        // 9. KateringOrder
        $order = KateringOrder::create([
            'nomor_pesanan'    => 'KAT-2026-0001',
            'id_user'          => $user->id_user,
            'nama_pemesan'     => 'Bapak Hendra',
            'no_whatsapp'      => '081234567890',
            'tanggal_kirim'    => now()->addDays(3)->toDateString(),
            'jam_kirim'        => '11:30:00',
            'total_boks_pesan' => 50,
            'total_boks_bonus' => 2,
            'total_tagihan'    => 1500000,
            'nominal_dp'       => 750000,
            'sisa_tagihan'     => 750000,
            'status_pesanan'   => 'diproses',
        ]);
        $this->assertEquals($user->id_user, $order->user->id_user);
        $this->assertTrue($user->kateringOrders->contains($order));

        // 10. KateringDetail
        $kateringDetail = KateringDetail::create([
            'id_katering'  => $order->id_katering,
            'id_menu'      => $menu->id_menu,
            'jumlah_boks'  => 50,
            'harga_satuan' => 30000,
            'subtotal'     => 1500000,
        ]);
        $this->assertEquals($order->id_katering, $kateringDetail->order->id_katering);
        $this->assertEquals($menu->id_menu, $kateringDetail->menu->id_menu);

        // 11. KategoriPengeluaran
        $katPengeluaran = KategoriPengeluaran::create([
            'nama_kategori_pengeluaran' => 'Bahan Baku Pasar',
            'status_aktif'              => 1,
        ]);
        $this->assertDatabaseHas('tigaputri_md_kategori_pengeluaran', [
            'id_kategori_pengeluaran' => $katPengeluaran->id_kategori_pengeluaran,
        ]);

        // 12. Pengeluaran
        $pengeluaran = Pengeluaran::create([
            'id_user'                 => $user->id_user,
            'id_kategori_pengeluaran' => $katPengeluaran->id_kategori_pengeluaran,
            'tanggal_pengeluaran'     => now()->toDateString(),
            'nominal'                 => 250000,
            'sumber_dana'             => 'kas_laci',
            'keterangan'              => 'Belanja cabai merah dan kelapa parut',
        ]);
        $this->assertEquals($user->id_user, $pengeluaran->user->id_user);
        $this->assertEquals($katPengeluaran->id_kategori_pengeluaran, $pengeluaran->kategori->id_kategori_pengeluaran);
        $this->assertTrue($user->pengeluaran->contains($pengeluaran));

        // 13. CmsBeranda
        $cmsBeranda = CmsBeranda::create([
            'hero_title'            => 'Masakan Padang Tiga Putri - Cita Rasa Otentik Minang',
            'jam_buka'              => '08:00 - 22:00 WIB',
            'no_whatsapp_reservasi' => '081234567890',
        ]);
        $this->assertDatabaseHas('tigaputri_md_cms_beranda', [
            'id_cms_beranda' => $cmsBeranda->id_cms_beranda,
        ]);

        // 14. CmsMenuFavorit
        $favorit = CmsMenuFavorit::create([
            'id_menu'       => $menu->id_menu,
            'badge_khusus'  => 'Best Seller',
            'urutan_tampil' => 1,
        ]);
        $this->assertEquals($menu->id_menu, $favorit->menu->id_menu);
        $this->assertTrue($menu->cmsMenuFavorits->contains($favorit));
    }
}
