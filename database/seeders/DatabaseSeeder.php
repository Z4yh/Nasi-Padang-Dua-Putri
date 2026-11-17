<?php

namespace Database\Seeders;

use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\SettingKatering;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@tigaputri.com'],
            [
                'nama_lengkap' => 'Super Administrator',
                'username'     => 'superadmin',
                'password'     => Hash::make('password'),
                'role'         => 'super_admin',
                'status_aktif' => 1,
            ]
        );

        // 2. Akun Penjual / Kasir
        User::updateOrCreate(
            ['email' => 'penjual@tigaputri.com'],
            [
                'nama_lengkap' => 'Kasir Penjual',
                'username'     => 'penjual',
                'password'     => Hash::make('password'),
                'role'         => 'penjual',
                'status_aktif' => 1,
            ]
        );

        // 3. Akun Pelanggan
        User::updateOrCreate(
            ['email' => 'pelanggan@tigaputri.com'],
            [
                'nama_lengkap' => 'Pelanggan Setia',
                'username'     => 'pelanggan',
                'password'     => Hash::make('password'),
                'role'         => 'pelanggan',
                'status_aktif' => 1,
            ]
        );

        // 4. Default Pengaturan Katering
        SettingKatering::updateOrCreate(
            ['id_setting_katering' => 1],
            [
                'min_order_bonus'  => 20,
                'jumlah_bonus_box' => 1,
                'min_dp_persen'    => 50,
            ]
        );

        // 5. Sample Data Master Kategori & Menu Awal
        $lauk = KategoriMenu::updateOrCreate(
            ['slug_kategori' => 'lauk-pauk-utama'],
            [
                'nama_kategori' => 'Lauk Pauk Utama',
                'urutan'        => 1,
            ]
        );

        $sayur = KategoriMenu::updateOrCreate(
            ['slug_kategori' => 'sayuran-dan-kuah'],
            [
                'nama_kategori' => 'Sayuran & Kuah',
                'urutan'        => 2,
            ]
        );

        $minum = KategoriMenu::updateOrCreate(
            ['slug_kategori' => 'minuman-dan-es'],
            [
                'nama_kategori' => 'Minuman & Es',
                'urutan'        => 3,
            ]
        );

        Menu::updateOrCreate(
            ['slug_menu' => 'rendang-daging-sapi-spesial'],
            [
                'id_kategori_menu' => $lauk->id_kategori_menu,
                'nama_menu'        => 'Rendang Daging Sapi Spesial',
                'harga'            => 25000,
                'deskripsi'        => 'Daging sapi pilihan dimasak empuk dengan racikan bumbu rempah kelapa khas Minang asli.',
                'status_stok'      => 'tersedia',
            ]
        );

        Menu::updateOrCreate(
            ['slug_menu' => 'ayam-pop-khas-tiga-putri'],
            [
                'id_kategori_menu' => $lauk->id_kategori_menu,
                'nama_menu'        => 'Ayam Pop Khas Tiga Putri',
                'harga'            => 20000,
                'deskripsi'        => 'Ayam kampung gurih dan lembut dengan sajian sambal tomat khas ayam pop.',
                'status_stok'      => 'tersedia',
            ]
        );

        Menu::updateOrCreate(
            ['slug_menu' => 'gulai-tunjang-kikil'],
            [
                'id_kategori_menu' => $lauk->id_kategori_menu,
                'nama_menu'        => 'Gulai Tunjang Kikil',
                'harga'            => 28000,
                'deskripsi'        => 'Kikil tunjang lembut dengan kuah gulai kental kuning beraroma sedap.',
                'status_stok'      => 'tersedia',
            ]
        );

        Menu::updateOrCreate(
            ['slug_menu' => 'gulai-daun-singkong'],
            [
                'id_kategori_menu' => $sayur->id_kategori_menu,
                'nama_menu'        => 'Gulai Daun Singkong',
                'harga'            => 6000,
                'deskripsi'        => 'Daun singkong muda berpadu kuah santan gurih.',
                'status_stok'      => 'tersedia',
            ]
        );

        Menu::updateOrCreate(
            ['slug_menu' => 'es-teh-manis-padang'],
            [
                'id_kategori_menu' => $minum->id_kategori_menu,
                'nama_menu'        => 'Es Teh Manis Padang',
                'harga'            => 5000,
                'deskripsi'        => 'Teh wangi pekat khas seduhan rumah makan Padang dingin menyegarkan.',
                'status_stok'      => 'tersedia',
            ]
        );
    }
}
