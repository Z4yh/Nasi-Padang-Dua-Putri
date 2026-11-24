<?php

namespace Tests\Feature\Keuangan;

use App\Models\Expense;
use App\Models\KateringOrder;
use App\Models\TransaksiKasir;
use App\Models\User;
use App\Services\Keuangan\CashFlowPeriodHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceApiTest extends TestCase
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
     * Kriteria 1: Tanpa login -> 401, non-admin (pelanggan) -> 403 ("Tidak punya akses").
     */
    public function test_auth_and_role_access(): void
    {
        // 1. Unauthenticated -> 401
        $this->getJson('/api/finance/summary')->assertStatus(401);
        $this->getJson('/api/finance/entries')->assertStatus(401);
        $this->postJson('/api/finance/expenses', [])->assertStatus(401);
        $this->patchJson('/api/finance/expenses/1', [])->assertStatus(401);
        $this->deleteJson('/api/finance/expenses/1')->assertStatus(401);

        // 2. Non-admin (pelanggan) -> 403 dengan pesan 'Tidak punya akses'
        $resSummary = $this->actingAs($this->pelanggan)->getJson('/api/finance/summary');
        $resSummary->assertStatus(403);
        $this->assertStringContainsString('Tidak punya akses', $resSummary->json('message'));

        $resEntries = $this->actingAs($this->pelanggan)->getJson('/api/finance/entries');
        $resEntries->assertStatus(403);
        $this->assertStringContainsString('Tidak punya akses', $resEntries->json('message'));

        $resPost = $this->actingAs($this->pelanggan)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 50000,
            'fund_source' => 'kas_tunai',
        ]);
        $resPost->assertStatus(403);
        $this->assertStringContainsString('Tidak punya akses', $resPost->json('message'));

        // 3. Admin (penjual / super_admin) -> 200
        $this->actingAs($this->admin)->getJson('/api/finance/summary')->assertStatus(200);
    }

    /**
     * Kriteria 2: GET /api/finance/summary menghasilkan angka yang konsisten dengan rumus PRD.
     */
    public function test_summary_returns_exact_prd_benchmark_data(): void
    {
        $today = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);

        // Setup POS: 3.280.000 (Tunai 2.140.000, QRIS 1.140.000)
        TransaksiKasir::forceCreate([
            'nomor_transaksi' => 'TP-20240524-0001',
            'id_user' => $this->admin->id_user,
            'metode_pembayaran' => 'tunai',
            'subtotal' => 2140000,
            'total_bayar' => 2140000,
            'kembalian' => 0,
            'created_at' => $today->copy()->setTime(9, 30),
            'updated_at' => $today->copy()->setTime(9, 30),
        ]);
        TransaksiKasir::forceCreate([
            'nomor_transaksi' => 'TP-20240524-0002',
            'id_user' => $this->admin->id_user,
            'metode_pembayaran' => 'qris',
            'subtotal' => 1140000,
            'total_bayar' => 1140000,
            'kembalian' => 0,
            'created_at' => $today->copy()->setTime(11, 45),
            'updated_at' => $today->copy()->setTime(11, 45),
        ]);

        // Setup Katering: 2.200.000 (1.500.000 lunas + 700.000 DP 50%)
        KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-204',
            'id_user' => $this->admin->id_user,
            'nama_pemesan' => 'Nasi Kotak BPN',
            'no_whatsapp' => '08111111111',
            'tanggal_kirim' => '2024-05-24',
            'jam_kirim' => '12:00:00',
            'total_boks_pesan' => 40,
            'total_tagihan' => 1500000,
            'nominal_dp' => 0,
            'sisa_tagihan' => 0,
            'status_pesanan' => 'selesai',
            'created_at' => $today->copy()->subDay(),
            'updated_at' => $today->copy()->setTime(14, 15),
        ]);
        KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-206',
            'id_user' => $this->admin->id_user,
            'nama_pemesan' => 'Prasmanan Syukuran',
            'no_whatsapp' => '08222222222',
            'tanggal_kirim' => '2024-05-26',
            'jam_kirim' => '10:00:00',
            'total_boks_pesan' => 50,
            'total_tagihan' => 1400000,
            'nominal_dp' => 700000,
            'sisa_tagihan' => 700000,
            'status_pesanan' => 'diproses',
            'created_at' => $today->copy()->setTime(13, 0),
            'updated_at' => $today->copy()->setTime(13, 0),
        ]);

        // Setup Pengeluaran: Pasar Subuh 1.650.000, Operasional 410.000
        Expense::create([
            'category' => 'bahan_pasar',
            'amount' => 1650000,
            'note' => 'Belanja Pasar Sukatani',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $today->copy()->setTime(6, 0),
            'created_by' => $this->admin->id_user,
        ]);
        Expense::create([
            'category' => 'gas_galon',
            'amount' => 410000,
            'note' => 'Beli Tabung Gas & Galon',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $today->copy()->setTime(8, 30),
            'created_by' => $this->admin->id_user,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/finance/summary?period=day&date=2024-05-24');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertSame('day', $data['period']);
        $this->assertSame('Jumat, 24 Mei 2024', $data['label']);
        $this->assertSame(3420000, $data['net_profit']);
        $this->assertSame(62.4, $data['margin_pct']);
        $this->assertSame('vs kemarin', $data['compare_label']);
        $this->assertSame(5480000, $data['total_in']);
        $this->assertSame(2060000, $data['total_out']);
        $this->assertSame(72.7, $data['in_pct']);
        $this->assertSame(27.3, $data['out_pct']);

        $this->assertSame(3280000, $data['in_breakdown']['pos']);
        $this->assertSame(2200000, $data['in_breakdown']['catering']);
        $this->assertSame(1650000, $data['out_breakdown']['pasar_subuh']);
        $this->assertSame(410000, $data['out_breakdown']['operasional']);

        // POS sources
        $this->assertSame(3280000, $data['sources']['pos']['total']);
        $this->assertSame(2, $data['sources']['pos']['tx_count']);
        $this->assertSame(1, $data['sources']['pos']['cash_count']);
        $this->assertSame(1, $data['sources']['pos']['qris_count']);
        $this->assertSame(2140000, $data['sources']['pos']['cash_total']);
        $this->assertSame(1140000, $data['sources']['pos']['qris_total']);

        // Catering sources
        $this->assertSame(2200000, $data['sources']['catering']['total']);
        $this->assertSame(2, $data['sources']['catering']['order_count']);
        $this->assertCount(2, $data['sources']['catering']['items']);

        // Item 1 (DP 50%)
        $itemDp = collect($data['sources']['catering']['items'])->firstWhere('status', 'dp');
        $this->assertNotNull($itemDp);
        $this->assertSame('KT-206', $itemDp['code']);
        $this->assertSame(700000, $itemDp['amount']);
        $this->assertSame(50, $itemDp['dp_pct']);

        // Item 2 (Lunas)
        $itemLunas = collect($data['sources']['catering']['items'])->firstWhere('status', 'lunas');
        $this->assertNotNull($itemLunas);
        $this->assertSame('KT-204', $itemLunas['code']);
        $this->assertSame(1500000, $itemLunas['amount']);
    }

    /**
     * Uji periode summary dan compare_label.
     */
    public function test_summary_periods_and_compare_labels(): void
    {
        $resDay = $this->actingAs($this->admin)->getJson('/api/finance/summary?period=day&date=2024-05-24');
        $this->assertSame('vs kemarin', $resDay->json('compare_label'));

        $resWeek = $this->actingAs($this->admin)->getJson('/api/finance/summary?period=week&date=2024-05-24');
        $this->assertSame('vs minggu lalu', $resWeek->json('compare_label'));

        $resMonth = $this->actingAs($this->admin)->getJson('/api/finance/summary?period=month&date=2024-05-24');
        $this->assertSame('vs bulan lalu', $resMonth->json('compare_label'));

        $resYear = $this->actingAs($this->admin)->getJson('/api/finance/summary?period=year&date=2024-05-24');
        $this->assertSame('vs tahun lalu', $resYear->json('compare_label'));

        // Parameter tidak valid -> 422
        $this->actingAs($this->admin)->getJson('/api/finance/summary?period=century')->assertStatus(422);
        $this->actingAs($this->admin)->getJson('/api/finance/summary?date=invalid-date')->assertStatus(422);
    }

    /**
     * Kriteria 3: GET /api/finance/entries mendukung filter all/in/out, pagination, dan ordering.
     */
    public function test_entries_filtering_and_pagination(): void
    {
        $today = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);

        TransaksiKasir::forceCreate([
            'nomor_transaksi' => 'TP-001',
            'id_user' => $this->admin->id_user,
            'metode_pembayaran' => 'tunai',
            'subtotal' => 100000,
            'total_bayar' => 100000,
            'kembalian' => 0,
            'created_at' => $today->copy()->setTime(9, 0),
            'updated_at' => $today->copy()->setTime(9, 0),
        ]);

        Expense::create([
            'category' => 'gas_galon',
            'amount' => 50000,
            'note' => 'Beli Gas',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $today->copy()->setTime(10, 0),
            'created_by' => $this->admin->id_user,
        ]);

        // Filter 'all' -> 2 entri
        $resAll = $this->actingAs($this->admin)->getJson('/api/finance/entries?period=day&date=2024-05-24&type=all');
        $resAll->assertStatus(200);
        $this->assertSame(2, $resAll->json('total_count'));
        $this->assertCount(2, $resAll->json('items'));

        // Verifikasi flag editable
        $items = $resAll->json('items');
        $expenseItem = collect($items)->firstWhere('source', 'expense');
        $posItem = collect($items)->firstWhere('source', 'pos');

        $this->assertTrue($expenseItem['editable']);
        $this->assertFalse($posItem['editable']);

        // Filter 'in' -> 1 entri (POS)
        $resIn = $this->actingAs($this->admin)->getJson('/api/finance/entries?period=day&date=2024-05-24&type=in');
        $this->assertSame(1, $resIn->json('total_count'));
        $this->assertSame('in', $resIn->json('items.0.type'));

        // Filter 'out' -> 1 entri (Expense)
        $resOut = $this->actingAs($this->admin)->getJson('/api/finance/entries?period=day&date=2024-05-24&type=out');
        $this->assertSame(1, $resOut->json('total_count'));
        $this->assertSame('out', $resOut->json('items.0.type'));

        // Pagination: limit=1
        $resPage1 = $this->actingAs($this->admin)->getJson('/api/finance/entries?period=day&date=2024-05-24&type=all&limit=1');
        $this->assertCount(1, $resPage1->json('items'));
        $this->assertNotNull($resPage1->json('next_cursor'));

        // Page 2 dengan next_cursor
        $cursor = $resPage1->json('next_cursor');
        $resPage2 = $this->actingAs($this->admin)->getJson("/api/finance/entries?period=day&date=2024-05-24&type=all&limit=1&cursor={$cursor}");
        $this->assertCount(1, $resPage2->json('items'));
        $this->assertNull($resPage2->json('next_cursor'));
        $this->assertNotSame($resPage1->json('items.0.id'), $resPage2->json('items.0.id'));
    }

    /**
     * Kriteria 4: POST /api/finance/expenses validasi penolakan input tidak valid (422 bahasa Indonesia).
     */
    public function test_post_expense_validations(): void
    {
        // Kategori palsu -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'kategori_palsu',
            'amount' => 50000,
            'fund_source' => 'kas_tunai',
        ])->assertStatus(422)->assertJsonValidationErrors(['category']);

        // Amount <= 0 -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 0,
            'fund_source' => 'kas_tunai',
        ])->assertStatus(422)->assertJsonValidationErrors(['amount']);

        // Amount desimal -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 12500.50,
            'fund_source' => 'kas_tunai',
        ])->assertStatus(422)->assertJsonValidationErrors(['amount']);

        // Kategori lain_lain tanpa catatan -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'lain_lain',
            'amount' => 50000,
            'fund_source' => 'kas_tunai',
        ])->assertStatus(422)->assertJsonValidationErrors(['note']);

        // Note > 200 karakter -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 50000,
            'note' => str_repeat('x', 201),
            'fund_source' => 'kas_tunai',
        ])->assertStatus(422)->assertJsonValidationErrors(['note']);

        // Waktu di masa depan -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 50000,
            'fund_source' => 'kas_tunai',
            'occurred_at' => now()->addDays(2)->toIso8601String(),
        ])->assertStatus(422)->assertJsonValidationErrors(['occurred_at']);

        // Fund source palsu -> 422
        $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 50000,
            'fund_source' => 'crypto',
        ])->assertStatus(422)->assertJsonValidationErrors(['fund_source']);
    }

    /**
     * Kriteria 4 & 5: POST berhasil (201), PATCH berhasil (200), DELETE berhasil (204),
     * dan nilai summary serta entries langsung terupdate secara real-time.
     */
    public function test_crud_lifecycle_and_realtime_impact_on_summary_and_entries(): void
    {
        $today = now(CashFlowPeriodHelper::TIMEZONE)->format('Y-m-d');

        // 1. POST
        $createRes = $this->actingAs($this->admin)->postJson('/api/finance/expenses', [
            'category' => 'bahan_pasar',
            'amount' => 500000,
            'note' => 'Beli Daging Rendang',
            'fund_source' => 'kas_tunai',
        ]);
        $createRes->assertStatus(201);
        $expenseId = $createRes->json('id');
        $this->assertSame($this->admin->id_user, $createRes->json('created_by'));

        // Cek summary mencerminkan pengeluaran 500.000
        $sumRes1 = $this->actingAs($this->admin)->getJson("/api/finance/summary?period=day&date={$today}");
        $this->assertSame(500000, $sumRes1->json('total_out'));
        $this->assertSame(500000, $sumRes1->json('out_breakdown.pasar_subuh'));

        // 2. PATCH sebagian field (ubah nominal jadi 750.000)
        $patchRes = $this->actingAs($this->admin)->patchJson("/api/finance/expenses/{$expenseId}", [
            'amount' => 750000,
            'note' => 'Beli Daging & Bumbu Tambahan',
        ]);
        $patchRes->assertStatus(200);
        $this->assertSame(750000, $patchRes->json('amount'));
        $this->assertSame('Beli Daging & Bumbu Tambahan', $patchRes->json('note'));

        // Cek summary terupdate jadi 750.000
        $sumRes2 = $this->actingAs($this->admin)->getJson("/api/finance/summary?period=day&date={$today}");
        $this->assertSame(750000, $sumRes2->json('total_out'));

        // 3. DELETE (Soft delete) -> 204
        $deleteRes = $this->actingAs($this->admin)->deleteJson("/api/finance/expenses/{$expenseId}");
        $deleteRes->assertStatus(204);

        // Hapus ulang idempoten (item sudah tidak ada/terhapus) -> 404
        $this->actingAs($this->admin)->deleteJson("/api/finance/expenses/{$expenseId}")->assertStatus(404);

        // 4. Kriteria 6: Pengeluaran terhapus tidak muncul di summary maupun riwayat
        $sumRes3 = $this->actingAs($this->admin)->getJson("/api/finance/summary?period=day&date={$today}");
        $this->assertSame(0, $sumRes3->json('total_out'));

        $entriesRes = $this->actingAs($this->admin)->getJson("/api/finance/entries?period=day&date={$today}&type=out");
        $this->assertSame(0, $entriesRes->json('total_count'));
        $this->assertCount(0, $entriesRes->json('items'));
    }
}
