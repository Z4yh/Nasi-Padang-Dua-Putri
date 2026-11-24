<?php

namespace Tests\Unit\Keuangan;

use App\Services\Keuangan\CashFlowFormatter;
use PHPUnit\Framework\TestCase;

class CashFlowFormatterTest extends TestCase
{
    public function test_format_rupiah(): void
    {
        $this->assertSame('Rp 0', CashFlowFormatter::formatRupiah(0));
        $this->assertSame('Rp 1.234.567', CashFlowFormatter::formatRupiah(1234567));
        $this->assertSame('-Rp 1.234.567', CashFlowFormatter::formatRupiah(-1234567));
        $this->assertSame('Rp 5.480.000', CashFlowFormatter::formatRupiah(5480000));
    }

    public function test_format_ringkas(): void
    {
        $this->assertSame('3.280k', CashFlowFormatter::formatRingkas(3280000));
        $this->assertSame('2.200k', CashFlowFormatter::formatRingkas(2200000));
        $this->assertSame('1.650k', CashFlowFormatter::formatRingkas(1650000));
        $this->assertSame('410k', CashFlowFormatter::formatRingkas(410000));
        $this->assertSame('-410k', CashFlowFormatter::formatRingkas(-410000));
        $this->assertSame('500', CashFlowFormatter::formatRingkas(500));
        $this->assertSame('0', CashFlowFormatter::formatRingkas(0));
    }
}
