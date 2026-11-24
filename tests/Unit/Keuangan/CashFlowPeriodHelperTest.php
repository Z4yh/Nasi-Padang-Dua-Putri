<?php

namespace Tests\Unit\Keuangan;

use App\Services\Keuangan\CashFlowPeriodHelper;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CashFlowPeriodHelperTest extends TestCase
{
    public function test_day_period(): void
    {
        $ref = Carbon::parse('2024-05-24 14:15:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('day', $ref);

        $this->assertSame('day', $range['period']);
        $this->assertSame('2024-05-24 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-25 00:00:00', $range['end']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-23 00:00:00', $range['comparison_start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-24 00:00:00', $range['comparison_end']->format('Y-m-d H:i:s'));

        // Label hari (Jumat, 24 Mei 2024)
        $this->assertSame('Jumat, 24 Mei 2024', $range['label']);
        $this->assertSame('Kamis, 23 Mei 2024', $range['comparison_label']);
    }

    public function test_week_period_starts_on_monday(): void
    {
        // 24 Mei 2024 adalah hari Jumat. Senin minggu tersebut adalah 20 Mei 2024.
        $ref = Carbon::parse('2024-05-24 10:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('week', $ref);

        $this->assertSame('week', $range['period']);
        $this->assertSame('2024-05-20 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-27 00:00:00', $range['end']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-13 00:00:00', $range['comparison_start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-20 00:00:00', $range['comparison_end']->format('Y-m-d H:i:s'));

        // Label mingguan satu bulan: 20–26 Mei 2024
        $this->assertSame('20–26 Mei 2024', $range['label']);
        $this->assertSame('13–19 Mei 2024', $range['comparison_label']);
    }

    public function test_cross_month_week_label(): void
    {
        // 1 Mei 2024 adalah Rabu. Senin adalah 29 April 2024, Minggu adalah 5 Mei 2024.
        $ref = Carbon::parse('2024-05-01 12:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('week', $ref);

        $this->assertSame('2024-04-29 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-06 00:00:00', $range['end']->format('Y-m-d H:i:s'));
        $this->assertSame('29 Apr – 5 Mei 2024', $range['label']);
    }

    public function test_month_period(): void
    {
        $ref = Carbon::parse('2024-05-24 08:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('month', $ref);

        $this->assertSame('month', $range['period']);
        $this->assertSame('2024-05-01 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-06-01 00:00:00', $range['end']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-04-01 00:00:00', $range['comparison_start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-01 00:00:00', $range['comparison_end']->format('Y-m-d H:i:s'));

        $this->assertSame('Mei 2024', $range['label']);
        $this->assertSame('April 2024', $range['comparison_label']);
    }

    public function test_year_period(): void
    {
        $ref = Carbon::parse('2024-05-24 08:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('year', $ref);

        $this->assertSame('year', $range['period']);
        $this->assertSame('2024-01-01 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2025-01-01 00:00:00', $range['end']->format('Y-m-d H:i:s'));
        $this->assertSame('2023-01-01 00:00:00', $range['comparison_start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-01 00:00:00', $range['comparison_end']->format('Y-m-d H:i:s'));

        $this->assertSame('2024', $range['label']);
        $this->assertSame('2023', $range['comparison_label']);
    }

    public function test_midnight_boundary_wib(): void
    {
        $ref = Carbon::parse('2024-05-24 00:00:00', CashFlowPeriodHelper::TIMEZONE);
        $range = CashFlowPeriodHelper::getPeriodRange('day', $ref);

        $this->assertSame('2024-05-24 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2024-05-25 00:00:00', $range['end']->format('Y-m-d H:i:s'));
    }

    public function test_invalid_period_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CashFlowPeriodHelper::getPeriodRange('decade');
    }
}
