<?php

namespace App\Services\Keuangan;

class CashFlowCalculator
{
    public const GROUP_PASAR_SUBUH = 'pasar_subuh';
    public const GROUP_OPERASIONAL = 'operasional';

    /**
     * Hitung ringkasan arus kas (total masuk, total keluar, laba bersih, margin %, kas masuk %, kas keluar %, pertumbuhan %).
     *
     * @param int $masukPos
     * @param int $masukKatering
     * @param int $keluarPasarSubuh
     * @param int $keluarOperasional
     * @param int|null $labaPembanding Laba bersih periode sebelumnya (bila ada)
     * @return array{
     *     total_masuk: int,
     *     total_keluar: int,
     *     laba_bersih: int,
     *     margin_persen: float,
     *     kas_masuk_persen: float,
     *     kas_keluar_persen: float,
     *     pertumbuhan_persen: float|null
     * }
     */
    public static function calculateSummary(
        int $masukPos,
        int $masukKatering,
        int $keluarPasarSubuh,
        int $keluarOperasional,
        ?int $labaPembanding = null
    ): array {
        $totalMasuk = $masukPos + $masukKatering;
        $totalKeluar = $keluarPasarSubuh + $keluarOperasional;
        $labaBersih = $totalMasuk - $totalKeluar;

        // Margin %: labaBersih / totalMasuk * 100 (jika totalMasuk == 0 -> 0.0)
        $marginPersen = $totalMasuk > 0
            ? round(($labaBersih / $totalMasuk) * 100, 1)
            : 0.0;

        // Komposisi Kas Masuk & Keluar %
        $totalArus = $totalMasuk + $totalKeluar;
        if ($totalArus > 0) {
            $kasMasukPersen = round(($totalMasuk / $totalArus) * 100, 1);
            $kasKeluarPersen = round(100.0 - $kasMasukPersen, 1);
        } else {
            $kasMasukPersen = 0.0;
            $kasKeluarPersen = 0.0;
        }

        // Pertumbuhan %: (labaIni - labaPembanding) / |labaPembanding| * 100
        // Jika labaPembanding == null atau 0 -> null
        $pertumbuhanPersen = null;
        if ($labaPembanding !== null && $labaPembanding !== 0) {
            $pertumbuhanPersen = round((($labaBersih - $labaPembanding) / abs($labaPembanding)) * 100, 1);
        }

        return [
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'laba_bersih' => $labaBersih,
            'margin_persen' => $marginPersen,
            'kas_masuk_persen' => $kasMasukPersen,
            'kas_keluar_persen' => $kasKeluarPersen,
            'pertumbuhan_persen' => $pertumbuhanPersen,
        ];
    }

    /**
     * Memetakan kategori pengeluaran ke grup ringkasan (pasar_subuh atau operasional).
     */
    public static function mapCategoryToGroup(string $category): string
    {
        return match ($category) {
            'bahan_pasar' => self::GROUP_PASAR_SUBUH,
            'gas_galon', 'upah_makan', 'listrik_air', 'lain_lain' => self::GROUP_OPERASIONAL,
            default => self::GROUP_OPERASIONAL,
        };
    }

    /**
     * Label grup ringkasan untuk tampilan.
     */
    public static function getGroupLabel(string $group): string
    {
        return match ($group) {
            self::GROUP_PASAR_SUBUH => 'Pasar Subuh',
            self::GROUP_OPERASIONAL => 'Operasional',
            default => ucfirst(str_replace('_', ' ', $group)),
        };
    }

    /**
     * Label kategori pengeluaran untuk tampilan.
     */
    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'bahan_pasar' => 'Bahan Pasar & Daging',
            'gas_galon' => 'Gas LPG & Galon',
            'upah_makan' => 'Upah / Uang Makan',
            'listrik_air' => 'Listrik & Air',
            'lain_lain' => 'Lain-lain',
            default => ucfirst(str_replace('_', ' ', $category)),
        };
    }
}
