<?php

namespace Tests\Feature\Keuangan;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceExportAndSheetTest extends TestCase
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
     * Kriteria 8: Hak akses ekspor dibatasi hanya untuk admin.
     */
    public function test_export_access_control(): void
    {
        // 1. Unauthenticated -> 401
        $this->getJson('/api/finance/export?format=csv')
            ->assertStatus(401);

        // 2. Non-admin (pelanggan) -> 403
        $this->actingAs($this->pelanggan)
            ->getJson('/api/finance/export?format=csv')
            ->assertStatus(403);

        // 3. Admin -> 200
        $this->actingAs($this->admin)
            ->getJson('/api/finance/export?format=csv')
            ->assertStatus(200);
    }

    /**
     * Parameter ekspor divalidasi dengan benar.
     */
    public function test_export_parameter_validation(): void
    {
        // Missing format -> 422
        $this->actingAs($this->admin)
            ->getJson('/api/finance/export')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['format']);

        // Invalid format -> 422
        $this->actingAs($this->admin)
            ->getJson('/api/finance/export?format=xlsx')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['format']);

        // Invalid period -> 422
        $this->actingAs($this->admin)
            ->getJson('/api/finance/export?format=csv&period=decade')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['period']);

        // Invalid date format -> 422
        $this->actingAs($this->admin)
            ->getJson('/api/finance/export?format=csv&date=24-11-2026')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /**
     * Ekspor CSV menghasilkan berkas dengan header, BOM UTF-8, dan data transaksi yang sesuai.
     */
    public function test_export_csv_output_and_headers(): void
    {
        Expense::factory()->create([
            'category' => 'bahan_pasar',
            'amount' => 250000,
            'note' => 'Beli Daging Rendang 3kg',
            'fund_source' => 'kas_tunai',
            'occurred_at' => Carbon::now('Asia/Jakarta'),
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/api/finance/export?format=csv&period=day');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('laporan-arus-kas_day_', $disposition);
        $this->assertStringContainsString('.csv', $disposition);

        // Mengambil isi stream CSV
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Verifikasi UTF-8 BOM (\xEF\xBB\xBF)
        $this->assertTrue(str_starts_with($csvContent, chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Verifikasi baris header CSV
        $this->assertStringContainsString('Tanggal,Jam,Jenis,Sumber,Kategori,Keterangan,"Metode / Sumber Dana",Nominal,Referensi', $csvContent);

        // Verifikasi isi data pengeluaran
        $this->assertStringContainsString('Keluar', $csvContent);
        $this->assertStringContainsString('Pengeluaran', $csvContent);
        $this->assertStringContainsString('Bahan Pasar & Daging', $csvContent);
        $this->assertStringContainsString('Beli Daging Rendang 3kg', $csvContent);
        $this->assertStringContainsString('TUNAI', $csvContent);
        $this->assertStringContainsString('250000', $csvContent);
    }

    /**
     * Ekspor CSV pada periode kosong tetap menghasilkan berkas dengan baris 'Tidak ada transaksi'.
     */
    public function test_export_csv_empty_period(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/api/finance/export?format=csv&period=day&date=2020-01-01');

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Tidak ada transaksi', $csvContent);
    }

    /**
     * Ekspor CSV mencegah kerentanan CSV Formula Injection.
     */
    public function test_export_csv_formula_injection_sanitization(): void
    {
        Expense::factory()->create([
            'category' => 'lain_lain',
            'amount' => 50000,
            'note' => '=1+1',
            'fund_source' => 'kas_tunai',
            'occurred_at' => Carbon::now('Asia/Jakarta'),
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/api/finance/export?format=csv&period=day');

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Nilai diawali tanda kutip tunggal aman: '=1+1
        $this->assertStringContainsString("'=1+1", $csvContent);
    }

    /**
     * Ekspor PDF merender tampilan A4 dengan data ringkasan.
     */
    public function test_export_pdf_renders_view_properly(): void
    {
        Expense::factory()->create([
            'category' => 'gas_galon',
            'amount' => 120000,
            'note' => 'Gas Elpiji 12kg',
            'fund_source' => 'kas_tunai',
            'occurred_at' => Carbon::now('Asia/Jakarta'),
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/api/finance/export?format=pdf&period=day');

        $response->assertStatus(200);
        $response->assertSee('RM PADANG TIGA PUTRI');
        $response->assertSee('Laporan Arus Kas');
        $response->assertSee('Laba Bersih Usaha');
        $response->assertSee('Gas Elpiji 12kg');
        $response->assertSee('Total Kas Masuk');
        $response->assertSee('Total Kas Keluar');
    }

    /**
     * Halaman Keuangan memuat elemen Bottom Sheet dan Toast.
     */
    public function test_keuangan_page_contains_bottom_sheet_and_toast(): void
    {
        $response = $this->actingAs($this->admin)->get('/keuangan');

        $response->assertStatus(200);
        $response->assertSee('CATAT PENGELUARAN');
        $response->assertSee('Kategori Pengeluaran');
        $response->assertSee('Bahan Pasar & Daging', false);
        $response->assertSee('Gas LPG & Galon', false);
        $response->assertSee('Jumlah Pengeluaran (Nominal)');
        $response->assertSee('Keterangan / Rincian Belanja');
        $response->assertSee('Kas Tunai Warung');
        $response->assertSee('Transfer Bank');
        $response->assertSee('Simpan');
        $response->assertSee('Buang Perubahan?');
        $response->assertSee('Hapus Pengeluaran Ini?');
    }
}
