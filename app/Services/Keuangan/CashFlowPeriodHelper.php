<?php

namespace App\Services\Keuangan;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use InvalidArgumentException;

class CashFlowPeriodHelper
{
    public const TIMEZONE = 'Asia/Jakarta';

    public const PERIOD_DAY = 'day';
    public const PERIOD_WEEK = 'week';
    public const PERIOD_MONTH = 'month';
    public const PERIOD_YEAR = 'year';

    public const VALID_PERIODS = [
        self::PERIOD_DAY,
        self::PERIOD_WEEK,
        self::PERIOD_MONTH,
        self::PERIOD_YEAR,
    ];

    /**
     * Hitung rentang periode setengah terbuka [start, end) dan periode pembanding.
     *
     * @param string $period 'day', 'week', 'month', 'year'
     * @param DateTimeInterface|string|null $referenceDate Tanggal acuan (default: saat ini di WIB)
     * @return array{
     *     period: string,
     *     start: Carbon,
     *     end: Carbon,
     *     comparison_start: Carbon,
     *     comparison_end: Carbon,
     *     label: string,
     *     comparison_label: string
     * }
     */
    public static function getPeriodRange(
        string $period,
        DateTimeInterface|string|null $referenceDate = null
    ): array {
        if (!in_array($period, self::VALID_PERIODS, true)) {
            throw new InvalidArgumentException("Periode '{$period}' tidak valid. Pilihan: day, week, month, year.");
        }

        $ref = self::parseReferenceDate($referenceDate);

        return match ($period) {
            self::PERIOD_DAY => self::calculateDayRange($ref),
            self::PERIOD_WEEK => self::calculateWeekRange($ref),
            self::PERIOD_MONTH => self::calculateMonthRange($ref),
            self::PERIOD_YEAR => self::calculateYearRange($ref),
        };
    }

    /**
     * Normalisasi tanggal acuan ke Carbon di zona waktu Asia/Jakarta.
     */
    public static function parseReferenceDate(DateTimeInterface|string|null $date = null): Carbon
    {
        if ($date === null) {
            return Carbon::now(self::TIMEZONE)->locale('id');
        }

        if ($date instanceof CarbonInterface) {
            return $date->copy()->setTimezone(self::TIMEZONE)->locale('id');
        }

        return Carbon::parse($date, self::TIMEZONE)->setTimezone(self::TIMEZONE)->locale('id');
    }

    /**
     * Rentang Harian: [00:00:00 hari ini, 00:00:00 besok).
     * Pembanding: [00:00:00 kemarin, 00:00:00 hari ini).
     */
    protected static function calculateDayRange(Carbon $ref): array
    {
        $start = $ref->copy()->startOfDay();
        $end = $start->copy()->addDay();

        $compStart = $start->copy()->subDay();
        $compEnd = $start->copy();

        return [
            'period' => self::PERIOD_DAY,
            'start' => $start,
            'end' => $end,
            'comparison_start' => $compStart,
            'comparison_end' => $compEnd,
            'label' => self::formatDayLabel($start),
            'comparison_label' => self::formatDayLabel($compStart),
        ];
    }

    /**
     * Rentang Mingguan: [Senin 00:00:00, Senin berikutnya 00:00:00).
     * Pembanding: [Senin minggu lalu, Senin minggu ini).
     */
    protected static function calculateWeekRange(Carbon $ref): array
    {
        // Minggu selalu dimulai hari Senin
        $start = $ref->copy()->startOfWeek(CarbonInterface::MONDAY)->startOfDay();
        $end = $start->copy()->addWeek();

        $compStart = $start->copy()->subWeek();
        $compEnd = $start->copy();

        return [
            'period' => self::PERIOD_WEEK,
            'start' => $start,
            'end' => $end,
            'comparison_start' => $compStart,
            'comparison_end' => $compEnd,
            'label' => self::formatWeekLabel($start, $end->copy()->subDay()),
            'comparison_label' => self::formatWeekLabel($compStart, $compEnd->copy()->subDay()),
        ];
    }

    /**
     * Rentang Bulanan: [Awal bulan 00:00:00, Awal bulan depan 00:00:00).
     * Pembanding: [Awal bulan lalu 00:00:00, Awal bulan ini 00:00:00).
     */
    protected static function calculateMonthRange(Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth()->startOfDay();
        $end = $start->copy()->addMonth();

        $compStart = $start->copy()->subMonth();
        $compEnd = $start->copy();

        return [
            'period' => self::PERIOD_MONTH,
            'start' => $start,
            'end' => $end,
            'comparison_start' => $compStart,
            'comparison_end' => $compEnd,
            'label' => self::formatMonthLabel($start),
            'comparison_label' => self::formatMonthLabel($compStart),
        ];
    }

    /**
     * Rentang Tahunan: [1 Jan 00:00:00, 1 Jan tahun depan 00:00:00).
     * Pembanding: [1 Jan tahun lalu, 1 Jan tahun ini).
     */
    protected static function calculateYearRange(Carbon $ref): array
    {
        $start = $ref->copy()->startOfYear()->startOfDay();
        $end = $start->copy()->addYear();

        $compStart = $start->copy()->subYear();
        $compEnd = $start->copy();

        return [
            'period' => self::PERIOD_YEAR,
            'start' => $start,
            'end' => $end,
            'comparison_start' => $compStart,
            'comparison_end' => $compEnd,
            'label' => self::formatYearLabel($start),
            'comparison_label' => self::formatYearLabel($compStart),
        ];
    }

    /**
     * Format label harian: "Rabu, 24 Mei 2024".
     */
    public static function formatDayLabel(Carbon $date): string
    {
        return $date->copy()->locale('id')->translatedFormat('l, j F Y');
    }

    /**
     * Format label mingguan: "20–26 Mei 2024" atau "28 Apr – 4 Mei 2024".
     */
    public static function formatWeekLabel(Carbon $start, Carbon $inclusiveEnd): string
    {
        $start = $start->copy()->locale('id');
        $end = $inclusiveEnd->copy()->locale('id');

        if ($start->year !== $end->year) {
            return $start->translatedFormat('j M Y') . ' – ' . $end->translatedFormat('j M Y');
        }

        if ($start->month !== $end->month) {
            return $start->translatedFormat('j M') . ' – ' . $end->translatedFormat('j M Y');
        }

        return $start->format('j') . '–' . $end->translatedFormat('j F Y');
    }

    /**
     * Format label bulanan: "Mei 2024".
     */
    public static function formatMonthLabel(Carbon $date): string
    {
        return $date->copy()->locale('id')->translatedFormat('F Y');
    }

    /**
     * Format label tahunan: "2024".
     */
    public static function formatYearLabel(Carbon $date): string
    {
        return $date->copy()->locale('id')->translatedFormat('Y');
    }
}
