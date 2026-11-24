<?php

namespace Tests\Unit\Keuangan;

use App\Services\Keuangan\CashFlowCalculator;
use PHPUnit\Framework\TestCase;

class CashFlowCalculatorTest extends TestCase
{
    /**
     * Uji nilai patokan sesuai PRD Bagian 5:
     * - Masuk: 5.480.000 (POS 3.280.000 + Katering 2.200.000)
     * - Keluar: 2.060.000 (Pasar Subuh 1.650.000 + Operasional 410.000)
     * - Laba: 3.420.000
     * - Margin: 62,4%
     * - Kas Masuk: 72,7% / Keluar: 27,3%
     */
    public function test_benchmark_calculation_from_prd(): void
    {
        $masukPos = 3280000;
        $masukKatering = 2200000;
        $keluarPasarSubuh = 1650000;
        $keluarOperasional = 410000;

        $result = CashFlowCalculator::calculateSummary(
            $masukPos,
            $masukKatering,
            $keluarPasarSubuh,
            $keluarOperasional,
            null
        );

        $this->assertSame(5480000, $result['total_masuk']);
        $this->assertSame(2060000, $result['total_keluar']);
        $this->assertSame(3420000, $result['laba_bersih']);
        $this->assertSame(62.4, $result['margin_persen']);
        $this->assertSame(72.7, $result['kas_masuk_persen']);
        $this->assertSame(27.3, $result['kas_keluar_persen']);
        $this->assertNull($result['pertumbuhan_persen']);
    }

    /**
     * Uji kasus tepi: total masuk dan keluar adalah 0.
     */
    public function test_edge_case_zero_income_and_expense(): void
    {
        $result = CashFlowCalculator::calculateSummary(0, 0, 0, 0, null);

        $this->assertSame(0, $result['total_masuk']);
        $this->assertSame(0, $result['total_keluar']);
        $this->assertSame(0, $result['laba_bersih']);
        $this->assertSame(0.0, $result['margin_persen']);
        $this->assertSame(0.0, $result['kas_masuk_persen']);
        $this->assertSame(0.0, $result['kas_keluar_persen']);
        $this->assertNull($result['pertumbuhan_persen']);
    }

    /**
     * Uji kasus tepi: laba bersih negatif (pengeluaran lebih besar dari pemasukan).
     */
    public function test_edge_case_negative_profit(): void
    {
        $result = CashFlowCalculator::calculateSummary(1000000, 0, 1500000, 500000, 500000);

        $this->assertSame(1000000, $result['total_masuk']);
        $this->assertSame(2000000, $result['total_keluar']);
        $this->assertSame(-1000000, $result['laba_bersih']);
        $this->assertSame(-100.0, $result['margin_persen']);
        $this->assertSame(33.3, $result['kas_masuk_persen']);
        $this->assertSame(66.7, $result['kas_keluar_persen']);
        // Pertumbuhan: (-1000000 - 500000) / 500000 * 100 = -300%
        $this->assertSame(-300.0, $result['pertumbuhan_persen']);
    }

    /**
     * Uji pertumbuhan % saat pembanding bernilai 0 atau tidak ada data.
     */
    public function test_growth_when_comparison_profit_is_zero_or_null(): void
    {
        $resNull = CashFlowCalculator::calculateSummary(1000000, 0, 500000, 0, null);
        $this->assertNull($resNull['pertumbuhan_persen']);

        $resZero = CashFlowCalculator::calculateSummary(1000000, 0, 500000, 0, 0);
        $this->assertNull($resZero['pertumbuhan_persen']);
    }

    /**
     * Uji pertumbuhan positif dan pembanding negatif.
     */
    public function test_growth_with_positive_and_negative_comparison(): void
    {
        // Positif: Laba ini 1.500.000, laba lalu 1.000.000 -> +50.0%
        $resPositive = CashFlowCalculator::calculateSummary(2000000, 0, 500000, 0, 1000000);
        $this->assertSame(50.0, $resPositive['pertumbuhan_persen']);

        // Pembanding minus: Laba ini 500.000, laba lalu -500.000 -> (500000 - (-500000)) / 500000 * 100 = +200.0%
        $resFromNegative = CashFlowCalculator::calculateSummary(1000000, 0, 500000, 0, -500000);
        $this->assertSame(200.0, $resFromNegative['pertumbuhan_persen']);
    }

    /**
     * Uji pemetaan kategori ke grup ringkasan (Pasar Subuh vs Operasional).
     */
    public function test_category_mapping(): void
    {
        $this->assertSame(CashFlowCalculator::GROUP_PASAR_SUBUH, CashFlowCalculator::mapCategoryToGroup('bahan_pasar'));

        $this->assertSame(CashFlowCalculator::GROUP_OPERASIONAL, CashFlowCalculator::mapCategoryToGroup('gas_galon'));
        $this->assertSame(CashFlowCalculator::GROUP_OPERASIONAL, CashFlowCalculator::mapCategoryToGroup('upah_makan'));
        $this->assertSame(CashFlowCalculator::GROUP_OPERASIONAL, CashFlowCalculator::mapCategoryToGroup('listrik_air'));
        $this->assertSame(CashFlowCalculator::GROUP_OPERASIONAL, CashFlowCalculator::mapCategoryToGroup('lain_lain'));

        $this->assertSame('Pasar Subuh', CashFlowCalculator::getGroupLabel(CashFlowCalculator::GROUP_PASAR_SUBUH));
        $this->assertSame('Operasional', CashFlowCalculator::getGroupLabel(CashFlowCalculator::GROUP_OPERASIONAL));
    }
}
