<?php

namespace Tests\Feature\Keuangan;

use App\Models\Expense;
use App\Models\KateringOrder;
use App\Models\TransaksiKasir;
use App\Models\User;
use App\Services\Keuangan\CashFlowPeriodHelper;
use App\Services\Keuangan\CashFlowService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CashFlowServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CashFlowService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->service = new CashFlowService();
    }

    /**
     * Validasi model Expense: amount harus > 0, note <= 200, category & fund_source valid.
     */
    public function test_expense_model_validations(): void
    {
        // Amount <= 0 harus gagal
        $this->expectException(InvalidArgumentException::class);
        Expense::create([
            'category' => 'bahan_pasar',
            'amount' => 0,
            'fund_source' => 'kas_tunai',
            'occurred_at' => now(),
            'created_by' => $this->user->id_user,
        ]);
    }

    public function test_expense_note_length_validation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Expense::create([
            'category' => 'bahan_pasar',
            'amount' => 100000,
            'note' => str_repeat('a', 201),
            'fund_source' => 'kas_tunai',
            'occurred_at' => now(),
            'created_by' => $this->user->id_user,
        ]);
    }

    public function test_expense_invalid_category_validation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Expense::create([
            'category' => 'kategori_palsu',
            'amount' => 100000,
            'fund_source' => 'kas_tunai',
            'occurred_at' => now(),
            'created_by' => $this->user->id_user,
        ]);
    }

    /**
     * Uji soft delete pada Expense: pengeluaran yang terhapus (deleted_at is not null)
     * tidak boleh masuk ke query getEntries maupun getSummary.
     */
    public function test_soft_deleted_expenses_are_excluded(): void
    {
        $date = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('day', $date);

        // Expense aktif
        $activeExpense = Expense::create([
            'category' => 'bahan_pasar',
            'amount' => 1000000,
            'note' => 'Belanja Daging',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $date,
            'created_by' => $this->user->id_user,
        ]);

        // Expense dihapus
        $deletedExpense = Expense::create([
            'category' => 'gas_galon',
            'amount' => 500000,
            'note' => 'Beli Gas',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $date,
            'created_by' => $this->user->id_user,
        ]);
        $deletedExpense->delete(); // soft delete

        $entries = $this->service->getEntries($range['start'], $range['end']);
        $this->assertCount(1, $entries);
        $this->assertSame('exp:' . $activeExpense->id, $entries->first()['id']);

        $aggregates = $this->service->calculatePeriodAggregates($range['start'], $range['end']);
        $this->assertSame(1000000, $aggregates['keluar_pasar_subuh']);
        $this->assertSame(0, $aggregates['keluar_operasional']);
        $this->assertSame(1, $aggregates['keluar_count']);
    }

    /**
     * Uji entri pesanan katering:
     * - Status dibatalkan diabaikan.
     * - DP dicatat saat pesanan dibuat.
     * - Pelunasan dicatat saat pesanan selesai.
     */
    public function test_catering_entries_logic(): void
    {
        $today = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('day', $today);

        // 1. Pesanan Katering dengan DP (status diproses)
        $order1 = KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-101',
            'id_user' => $this->user->id_user,
            'nama_pemesan' => 'Bpk Hendra',
            'no_whatsapp' => '08123456789',
            'tanggal_kirim' => '2024-05-25',
            'jam_kirim' => '11:00:00',
            'total_boks_pesan' => 50,
            'total_tagihan' => 1000000,
            'nominal_dp' => 400000,
            'sisa_tagihan' => 600000,
            'status_pesanan' => 'diproses',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        // 2. Pesanan Katering Dibatalkan (harus diabaikan)
        KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-999',
            'id_user' => $this->user->id_user,
            'nama_pemesan' => 'Dibatalkan',
            'no_whatsapp' => '08123456789',
            'tanggal_kirim' => '2024-05-24',
            'jam_kirim' => '12:00:00',
            'total_boks_pesan' => 20,
            'total_tagihan' => 500000,
            'nominal_dp' => 200000,
            'sisa_tagihan' => 300000,
            'status_pesanan' => 'dibatalkan',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        // 3. Pesanan Katering Lunas Langsung (selesai hari ini tanpa DP awal)
        $order3 = KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-102',
            'id_user' => $this->user->id_user,
            'nama_pemesan' => 'Ibu Siti',
            'no_whatsapp' => '08129876543',
            'tanggal_kirim' => '2024-05-24',
            'jam_kirim' => '13:00:00',
            'total_boks_pesan' => 30,
            'total_tagihan' => 600000,
            'nominal_dp' => 0,
            'sisa_tagihan' => 0,
            'status_pesanan' => 'selesai',
            'created_at' => $today->copy()->subDays(2),
            'updated_at' => $today,
        ]);

        $entries = $this->service->getEntries($range['start'], $range['end'], 'in');

        // Harus ada 2 entri pemasukan katering: DP KT-101 (400.000) dan Lunas KT-102 (600.000)
        $this->assertCount(2, $entries);
        $this->assertEquals(1000000, $entries->sum('amount'));

        $dpEntry = $entries->firstWhere('id', "kt:{$order1->id_katering}:dp");
        $this->assertNotNull($dpEntry);
        $this->assertSame(400000, $dpEntry['amount']);
        $this->assertStringContainsString('DP 40%', $dpEntry['subtitle']);

        $lunasEntry = $entries->firstWhere('id', "kt:{$order3->id_katering}:lunas");
        $this->assertNotNull($lunasEntry);
        $this->assertSame(600000, $lunasEntry['amount']);
    }

    /**
     * Uji integrasi lengkap dengan angka patokan PRD Bagian 5:
     * - POS: 3.280.000
     * - Katering: 2.200.000 (1.500.000 lunas + 700.000 DP)
     * - Pasar Subuh: 1.650.000
     * - Operasional: 410.000
     * Target:
     * - Total Masuk: 5.480.000
     * - Total Keluar: 2.060.000
     * - Laba Bersih: 3.420.000
     * - Margin: 62.4%
     * - Kas Masuk: 72.7%
     * - Kas Keluar: 27.3%
     */
    public function test_full_benchmark_end_to_end_from_prd(): void
    {
        $today = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('day', $today);

        // 1. POS Transactions: total 3.280.000 (2.140.000 Tunai + 1.140.000 QRIS)
        TransaksiKasir::forceCreate([
            'nomor_transaksi' => 'TP-20240524-0001',
            'id_user' => $this->user->id_user,
            'metode_pembayaran' => 'tunai',
            'subtotal' => 2140000,
            'total_bayar' => 2140000,
            'kembalian' => 0,
            'created_at' => $today->copy()->setTime(9, 30),
            'updated_at' => $today->copy()->setTime(9, 30),
        ]);

        TransaksiKasir::forceCreate([
            'nomor_transaksi' => 'TP-20240524-0002',
            'id_user' => $this->user->id_user,
            'metode_pembayaran' => 'qris',
            'subtotal' => 1140000,
            'total_bayar' => 1140000,
            'kembalian' => 0,
            'created_at' => $today->copy()->setTime(11, 45),
            'updated_at' => $today->copy()->setTime(11, 45),
        ]);

        // 2. Catering Orders: total 2.200.000 (1.500.000 Lunas + 700.000 DP)
        KateringOrder::forceCreate([
            'nomor_pesanan' => 'KT-204',
            'id_user' => $this->user->id_user,
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
            'id_user' => $this->user->id_user,
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

        // 3. Expenses: Pasar Subuh = 1.650.000, Operasional = 410.000
        Expense::create([
            'category' => 'bahan_pasar',
            'amount' => 1650000,
            'note' => 'Belanja Daging & Bumbu Pasar Sukatani',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $today->copy()->setTime(6, 0),
            'created_by' => $this->user->id_user,
        ]);

        Expense::create([
            'category' => 'gas_galon',
            'amount' => 410000,
            'note' => 'Beli Tabung Gas & Galon',
            'fund_source' => 'kas_tunai',
            'occurred_at' => $today->copy()->setTime(8, 30),
            'created_by' => $this->user->id_user,
        ]);

        // Eksekusi getSummary
        $summaryData = $this->service->getSummary('day', $today);

        $this->assertSame(3280000, $summaryData['aggregates']['masuk_pos']);
        $this->assertSame(2140000, $summaryData['aggregates']['masuk_pos_tunai']);
        $this->assertSame(1140000, $summaryData['aggregates']['masuk_pos_qris']);
        $this->assertSame(2200000, $summaryData['aggregates']['masuk_katering']);
        $this->assertSame(1650000, $summaryData['aggregates']['keluar_pasar_subuh']);
        $this->assertSame(410000, $summaryData['aggregates']['keluar_operasional']);

        $summary = $summaryData['summary'];
        $this->assertSame(5480000, $summary['total_masuk']);
        $this->assertSame(2060000, $summary['total_keluar']);
        $this->assertSame(3420000, $summary['laba_bersih']);
        $this->assertSame(62.4, $summary['margin_persen']);
        $this->assertSame(72.7, $summary['kas_masuk_persen']);
        $this->assertSame(27.3, $summary['kas_keluar_persen']);

        // Verifikasi entri seragam & pengurutan (terbaru di atas)
        $entries = $this->service->getEntries($range['start'], $range['end']);
        $this->assertCount(6, $entries);

        // Pastikan terurut dari terbaru ke terlama
        for ($i = 0; $i < $entries->count() - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $entries[$i + 1]['occurred_at']->getTimestamp(),
                $entries[$i]['occurred_at']->getTimestamp()
            );
        }

        // Cek struktur setiap entri memenuhi seragam
        foreach ($entries as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertContains($item['type'], ['in', 'out']);
            $this->assertContains($item['source'], ['pos', 'catering', 'expense']);
            $this->assertNotEmpty($item['title']);
            $this->assertNotEmpty($item['subtitle']);
            $this->assertContains($item['method'], ['tunai', 'qris', 'transfer']);
            $this->assertGreaterThan(0, $item['amount']);
            $this->assertInstanceOf(Carbon::class, $item['occurred_at']);
            $this->assertNotNull($item['ref_id']);
        }
    }
}
