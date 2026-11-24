<?php

namespace Tests\Feature\Keuangan;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->pelanggan = User::factory()->create([
            'role' => 'pelanggan',
        ]);
    }

    /**
     * Kriteria 1: Proteksi rute halaman Keuangan.
     * - Belum login -> redirect ke login (302).
     * - Non-admin (pelanggan) -> 403 dengan pesan 'Tidak punya akses'.
     * - Admin / super_admin -> 200 OK.
     */
    public function test_keuangan_page_access_control(): void
    {
        // 1. Unauthenticated -> redirect ke login
        $this->get('/keuangan')->assertRedirect(route('login'));

        // 2. Non-admin (pelanggan) -> 403
        $responseForbidden = $this->actingAs($this->pelanggan)->get('/keuangan');
        $responseForbidden->assertStatus(403);
        $this->assertStringContainsString('Tidak punya akses', $responseForbidden->getContent());

        // 3. Admin -> 200 OK
        $responseOk = $this->actingAs($this->admin)->get('/keuangan');
        $responseOk->assertStatus(200);
    }

    /**
     * Kriteria 1, 3, 5, 7: Struktur halaman Keuangan memuat seluruh section yang disyaratkan.
     */
    public function test_keuangan_page_renders_all_required_sections_and_components(): void
    {
        $response = $this->actingAs($this->admin)->get('/keuangan');

        $response->assertStatus(200);

        // Header & Aksi
        $response->assertSee('Laporan Arus Kas');
        $response->assertSee('Rekapitulasi otomatis dari Kasir POS &amp; Pesanan Katering.', false);
        $response->assertSee('Pengeluaran');

        // Filter Periode
        $response->assertSee('Periode Pembukuan');
        $response->assertSee('Hari Ini');
        $response->assertSee('Minggu Ini');
        $response->assertSee('Bulan Ini');
        $response->assertSee('Tahun Ini');

        // Kartu Laba Bersih Usaha
        $response->assertSee('LABA BERSIH USAHA');
        $response->assertSee('Margin Laba:');
        $response->assertSee('Rumus: Masuk – Keluar');
        $response->assertSee('Kas Masuk');
        $response->assertSee('Kas Keluar');

        // In Out Cards
        $response->assertSee('Total Masuk');
        $response->assertSee('Total Keluar');
        $response->assertSee('POS Kasir:');
        $response->assertSee('Katering:');
        $response->assertSee('Pasar Subuh:');
        $response->assertSee('Operasional:');

        // Sumber Pemasukan
        $response->assertSee('Sumber Pemasukan');
        $response->assertSee('Kasir POS (Warung Fisik)');
        $response->assertSee('Pesanan Katering');
        $response->assertSee('Tunai:');
        $response->assertSee('QRIS:');

        // Riwayat Arus Kas
        $response->assertSee('Riwayat Arus Kas');
        $response->assertSee('Semua');
        $response->assertSee('Kas Masuk (+)');
        $response->assertSee('Kas Keluar (–)');

        // Ekspor Pembukuan
        $response->assertSee('Ekspor Pembukuan');
        $response->assertSee('Unduh PDF');
        $response->assertSee('Excel CSV');
    }
}
