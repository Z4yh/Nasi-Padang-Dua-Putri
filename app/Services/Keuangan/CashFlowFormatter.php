<?php

namespace App\Services\Keuangan;

class CashFlowFormatter
{
    /**
     * Format angka ke format mata uang Rupiah standar Indonesia.
     * Contoh:
     *  1234567 -> "Rp 1.234.567"
     * -1234567 -> "-Rp 1.234.567"
     *  0       -> "Rp 0"
     */
    public static function formatRupiah(int|float $amount): string
    {
        $amountInt = (int) round($amount);

        if ($amountInt === 0) {
            return 'Rp 0';
        }

        if ($amountInt < 0) {
            return '-Rp ' . number_format(abs($amountInt), 0, ',', '.');
        }

        return 'Rp ' . number_format($amountInt, 0, ',', '.');
    }

    /**
     * Format ringkas untuk rincian kecil (misal: 3.280k, 1.650k, 410k).
     *
     * Aturan Pembulatan & Format:
     * - Jika |amount| >= 1.000:
     *     Nilai dibagi 1.000, dibulatkan ke bilangan bulat terdekat (round),
     *     lalu diformat dengan pemisah ribuan titik (.) dan diakhiri huruf 'k'.
     *     Contoh:
     *       3.280.000 -> 3.280k
     *       1.650.000 -> 1.650k
     *         410.000 -> 410k
     *        -410.000 -> -410k
     * - Jika |amount| < 1.000:
     *     Menampilkan nilai aslinya tanpa sufiks 'k'.
     */
    public static function formatRingkas(int|float $amount): string
    {
        $amountInt = (int) round($amount);
        $abs = abs($amountInt);

        if ($abs >= 1000) {
            $inThousands = (int) round($abs / 1000);
            $formatted = number_format($inThousands, 0, ',', '.') . 'k';
            return ($amountInt < 0 ? '-' : '') . $formatted;
        }

        return (string) $amountInt;
    }
}
