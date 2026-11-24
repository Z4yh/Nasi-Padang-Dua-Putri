<?php

namespace App\Services\Keuangan;

use App\Models\Expense;
use App\Models\KateringOrder;
use App\Models\TransaksiKasir;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Collection;

class CashFlowService
{
    /**
     * Mengambil entri arus kas gabungan (POS, Katering, Pengeluaran) dalam rentang periode.
     *
     * @param CarbonInterface $start Batas awal periode (inklusif)
     * @param CarbonInterface $end Batas akhir periode (eksklusif)
     * @param string|null $typeFilter 'in', 'out', atau null (semua)
     * @return Collection<int, array{
     *     id: string,
     *     type: string,
     *     source: string,
     *     title: string,
     *     subtitle: string,
     *     method: string,
     *     category: string|null,
     *     amount: int,
     *     occurred_at: Carbon,
     *     ref_id: int|string,
     *     editable: bool
     * }>
     */
    public function getEntries(
        CarbonInterface $start,
        CarbonInterface $end,
        ?string $typeFilter = null
    ): Collection {
        $entries = collect();

        // 1. Kasir POS (Pemasukan)
        if ($typeFilter === null || $typeFilter === 'all' || $typeFilter === 'in') {
            $posEntries = $this->fetchPosEntries($start, $end);
            $entries = $entries->concat($posEntries);

            // 2. Katering (Pemasukan)
            $cateringEntries = $this->fetchCateringEntries($start, $end);
            $entries = $entries->concat($cateringEntries);
        }

        // 3. Pengeluaran (Pengeluaran)
        if ($typeFilter === null || $typeFilter === 'all' || $typeFilter === 'out') {
            $expenseEntries = $this->fetchExpenseEntries($start, $end);
            $entries = $entries->concat($expenseEntries);
        }

        // Urutkan terbaru di atas (descending berdasarkan occurred_at, tie-break id descending)
        return $entries->sort(function (array $a, array $b) {
            $timeCmp = $b['occurred_at']->getTimestamp() <=> $a['occurred_at']->getTimestamp();
            if ($timeCmp !== 0) {
                return $timeCmp;
            }
            return strcmp((string) $b['id'], (string) $a['id']);
        })->values();
    }

    /**
     * Mengambil entri riwayat arus kas dengan dukungan cursor pagination.
     *
     * @param CarbonInterface $start
     * @param CarbonInterface $end
     * @param string $typeFilter 'all', 'in', 'out'
     * @param string|null $cursor Base64 encoded offset
     * @param int $limit Default 20, maks 50
     * @return array{
     *     items: array,
     *     total_count: int,
     *     next_cursor: string|null
     * }
     */
    public function getPaginatedEntries(
        CarbonInterface $start,
        CarbonInterface $end,
        string $typeFilter = 'all',
        ?string $cursor = null,
        int $limit = 20
    ): array {
        $filter = ($typeFilter === 'all') ? null : $typeFilter;
        $allEntries = $this->getEntries($start, $end, $filter);
        $totalCount = $allEntries->count();

        $limit = min(max(1, $limit), 50);

        $offset = 0;
        if ($cursor !== null && $cursor !== '') {
            $decoded = base64_decode($cursor, true);
            if ($decoded !== false && is_numeric($decoded)) {
                $offset = max(0, (int) $decoded);
            }
        }

        $items = $allEntries->slice($offset, $limit)->map(function (array $item) {
            return [
                'id' => $item['id'],
                'type' => $item['type'],
                'source' => $item['source'],
                'title' => $item['title'],
                'subtitle' => $item['subtitle'],
                'method' => $item['method'],
                'category' => $item['category'],
                'amount' => (int) $item['amount'],
                'occurred_at' => $item['occurred_at']->toIso8601String(),
                'ref_id' => $item['ref_id'],
                'editable' => (bool) $item['editable'],
                'note' => $item['note'] ?? null,
                'fund_source' => $item['fund_source'] ?? null,
            ];
        })->values()->all();

        $nextCursor = null;
        if ($offset + $limit < $totalCount) {
            $nextCursor = base64_encode((string) ($offset + $limit));
        }

        return [
            'items' => $items,
            'total_count' => $totalCount,
            'next_cursor' => $nextCursor,
        ];
    }

    /**
     * Mengambil entri pemasukan dari Kasir POS.
     * Hanya transaksi berstatus selesai/tercatat.
     */
    protected function fetchPosEntries(CarbonInterface $start, CarbonInterface $end): Collection
    {
        $transactions = TransaksiKasir::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->get();

        return $transactions->map(function (TransaksiKasir $tx) {
            $amount = (int) round((float) $tx->subtotal);
            $method = strtolower($tx->metode_pembayaran ?: 'tunai');
            $methodLabel = match ($method) {
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                default => 'Tunai',
            };

            return [
                'id' => 'pos:' . $tx->id_transaksi,
                'type' => 'in',
                'source' => 'pos',
                'title' => 'Kasir POS Order #' . $tx->nomor_transaksi,
                'subtitle' => $methodLabel . ' • Kasir Warung',
                'method' => $method,
                'category' => null,
                'amount' => $amount,
                'occurred_at' => Carbon::parse($tx->created_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE),
                'ref_id' => $tx->id_transaksi,
                'editable' => false,
            ];
        });
    }

    /**
     * Mengambil entri pemasukan dari Pesanan Katering.
     * Berdasarkan uang yang benar-benar diterima (DP dicatat saat tanggal pesanan dibuat,
     * pelunasan dicatat saat pesanan berstatus selesai).
     */
    protected function fetchCateringEntries(CarbonInterface $start, CarbonInterface $end): Collection
    {
        $entries = collect();

        $orders = KateringOrder::query()
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                      ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->get();

        foreach ($orders as $order) {
            $totalTagihan = (int) round((float) $order->total_tagihan);
            $nominalDp = (int) round((float) $order->nominal_dp);
            $sisaTagihan = max(0, $totalTagihan - $nominalDp);

            $createdAt = Carbon::parse($order->created_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE);
            $updatedAt = Carbon::parse($order->updated_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE);

            // 1. Catat entri DP jika ada nominal_dp dan waktu created_at dalam rentang [start, end)
            if ($nominalDp > 0 && $createdAt >= $start && $createdAt < $end) {
                $isFullAtDp = ($order->status_pesanan === 'selesai' && $sisaTagihan === 0 && $nominalDp >= $totalTagihan);
                $statusPercent = $totalTagihan > 0 ? (int) round(($nominalDp / $totalTagihan) * 100) : 100;
                $statusTag = $isFullAtDp ? 'Lunas' : "DP {$statusPercent}%";

                $entries->push([
                    'id' => "kt:{$order->id_katering}:dp",
                    'type' => 'in',
                    'source' => 'catering',
                    'title' => "Katering DP & Pelunasan (#{$order->nomor_pesanan})",
                    'subtitle' => "{$order->nama_pemesan} • {$statusTag}",
                    'method' => 'transfer',
                    'category' => null,
                    'amount' => $nominalDp,
                    'occurred_at' => $createdAt,
                    'ref_id' => $order->id_katering,
                    'editable' => false,
                ]);
            }

            // 2. Catat entri Pelunasan jika pesanan berstatus 'selesai' dan waktu penyelesaian dalam rentang [start, end)
            if ($order->status_pesanan === 'selesai' && $updatedAt >= $start && $updatedAt < $end) {
                if ($nominalDp <= 0 && $totalTagihan > 0) {
                    // Pembayaran lunas sekaligus saat selesai tanpa DP awal
                    $entries->push([
                        'id' => "kt:{$order->id_katering}:lunas",
                        'type' => 'in',
                        'source' => 'catering',
                        'title' => "Katering Pelunasan (#{$order->nomor_pesanan})",
                        'subtitle' => "{$order->nama_pemesan} • Lunas",
                        'method' => 'transfer',
                        'category' => null,
                        'amount' => $totalTagihan,
                        'occurred_at' => $updatedAt,
                        'ref_id' => $order->id_katering,
                        'editable' => false,
                    ]);
                } elseif ($sisaTagihan > 0 && $nominalDp > 0) {
                    // Pembayaran sisa tagihan saat pesanan selesai
                    $entries->push([
                        'id' => "kt:{$order->id_katering}:pelunasan",
                        'type' => 'in',
                        'source' => 'catering',
                        'title' => "Katering Pelunasan (#{$order->nomor_pesanan})",
                        'subtitle' => "{$order->nama_pemesan} • Pelunasan Sisa",
                        'method' => 'transfer',
                        'category' => null,
                        'amount' => $sisaTagihan,
                        'occurred_at' => $updatedAt,
                        'ref_id' => $order->id_katering,
                        'editable' => false,
                    ]);
                }
            }
        }

        return $entries;
    }

    /**
     * Rincian ringkasan pesanan katering untuk endpoint summary.
     * Mengembalikan total, order_count, dan top 3 items.
     *
     * @return array{total: int, order_count: int, items: array}
     */
    public function fetchCateringSummary(CarbonInterface $start, CarbonInterface $end): array
    {
        $items = [];
        $total = 0;

        $orders = KateringOrder::query()
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                      ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->get();

        foreach ($orders as $order) {
            $totalTagihan = (int) round((float) $order->total_tagihan);
            $nominalDp = (int) round((float) $order->nominal_dp);
            $sisaTagihan = max(0, $totalTagihan - $nominalDp);

            $createdAt = Carbon::parse($order->created_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE);
            $updatedAt = Carbon::parse($order->updated_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE);

            $porsiLabel = $order->total_boks_pesan > 0 ? " ({$order->total_boks_pesan} porsi)" : '';
            $title = $order->nama_pemesan . $porsiLabel;

            // 1. DP
            if ($nominalDp > 0 && $createdAt >= $start && $createdAt < $end) {
                $isFull = ($order->status_pesanan === 'selesai' && $sisaTagihan === 0 && $nominalDp >= $totalTagihan);
                $dpPct = $totalTagihan > 0 ? (int) round(($nominalDp / $totalTagihan) * 100) : 100;

                $item = [
                    'code' => $order->nomor_pesanan,
                    'title' => $title,
                    'amount' => $nominalDp,
                    'status' => $isFull ? 'lunas' : 'dp',
                ];
                if (! $isFull) {
                    $item['dp_pct'] = $dpPct;
                }

                $items[] = $item;
                $total += $nominalDp;
            }

            // 2. Selesai (Lunas / Pelunasan)
            if ($order->status_pesanan === 'selesai' && $updatedAt >= $start && $updatedAt < $end) {
                if ($nominalDp <= 0 && $totalTagihan > 0) {
                    $items[] = [
                        'code' => $order->nomor_pesanan,
                        'title' => $title,
                        'amount' => $totalTagihan,
                        'status' => 'lunas',
                    ];
                    $total += $totalTagihan;
                } elseif ($sisaTagihan > 0 && $nominalDp > 0) {
                    $items[] = [
                        'code' => $order->nomor_pesanan,
                        'title' => $title,
                        'amount' => $sisaTagihan,
                        'status' => 'lunas',
                    ];
                    $total += $sisaTagihan;
                }
            }
        }

        return [
            'total' => $total,
            'order_count' => count($items),
            'items' => array_slice($items, 0, 3),
        ];
    }

    /**
     * Mengambil entri pengeluaran dari tabel expenses (hanya baris aktif, deleted_at is null).
     */
    protected function fetchExpenseEntries(CarbonInterface $start, CarbonInterface $end): Collection
    {
        $expenses = Expense::query()
            ->whereNull('deleted_at')
            ->where('occurred_at', '>=', $start)
            ->where('occurred_at', '<', $end)
            ->get();

        return $expenses->map(function (Expense $exp) {
            $group = CashFlowCalculator::mapCategoryToGroup($exp->category);
            $categoryLabel = CashFlowCalculator::getCategoryLabel($exp->category);
            $method = $exp->fund_source === 'kas_tunai' ? 'tunai' : 'transfer';

            return [
                'id' => 'exp:' . $exp->id,
                'type' => 'out',
                'source' => 'expense',
                'title' => $exp->note ?: $categoryLabel,
                'subtitle' => $categoryLabel . ' • ' . ($exp->fund_source === 'kas_tunai' ? 'Kas Tunai Warung' : 'Transfer Bank'),
                'method' => $method,
                'category' => $exp->category,
                'amount' => (int) $exp->amount,
                'occurred_at' => Carbon::parse($exp->occurred_at)->setTimezone(CashFlowPeriodHelper::TIMEZONE),
                'ref_id' => $exp->id,
                'editable' => true,
                'note' => $exp->note,
                'fund_source' => $exp->fund_source,
            ];
        });
    }

    /**
     * Hitung ringkasan lengkap arus kas sesuai format resmi PRD 4.1 untuk GET /api/finance/summary.
     *
     * @param string $period 'day', 'week', 'month', 'year'
     * @param DateTimeInterface|string|null $referenceDate
     * @return array
     */
    public function getFinanceSummary(string $period = 'day', DateTimeInterface|string|null $referenceDate = null): array
    {
        $range = CashFlowPeriodHelper::getPeriodRange($period, $referenceDate);

        // Agregat periode utama
        $mainAggregates = $this->calculatePeriodAggregates($range['start'], $range['end']);

        // Agregat periode pembanding (untuk pertumbuhan %)
        $compAggregates = $this->calculatePeriodAggregates($range['comparison_start'], $range['comparison_end']);
        $labaPembanding = $compAggregates['laba_bersih'];

        // Rumus keuangan murni
        $summary = CashFlowCalculator::calculateSummary(
            $mainAggregates['masuk_pos'],
            $mainAggregates['masuk_katering'],
            $mainAggregates['keluar_pasar_subuh'],
            $mainAggregates['keluar_operasional'],
            $labaPembanding
        );

        $compareLabel = match ($period) {
            'day' => 'vs kemarin',
            'week' => 'vs minggu lalu',
            'month' => 'vs bulan lalu',
            'year' => 'vs tahun lalu',
            default => 'vs periode lalu',
        };

        // Rincian Katering
        $cateringSummary = $this->fetchCateringSummary($range['start'], $range['end']);

        return [
            'period' => $range['period'],
            'range' => [
                'start' => $range['start']->toIso8601String(),
                'end' => $range['end']->toIso8601String(),
            ],
            'label' => $range['label'],
            'net_profit' => $summary['laba_bersih'],
            'margin_pct' => $summary['margin_persen'],
            'growth_pct' => $summary['pertumbuhan_persen'],
            'compare_label' => $compareLabel,
            'total_in' => $summary['total_masuk'],
            'total_out' => $summary['total_keluar'],
            'in_pct' => $summary['kas_masuk_persen'],
            'out_pct' => $summary['kas_keluar_persen'],
            'in_breakdown' => [
                'pos' => $mainAggregates['masuk_pos'],
                'catering' => $mainAggregates['masuk_katering'],
            ],
            'out_breakdown' => [
                'pasar_subuh' => $mainAggregates['keluar_pasar_subuh'],
                'operasional' => $mainAggregates['keluar_operasional'],
            ],
            'sources' => [
                'pos' => [
                    'total' => $mainAggregates['masuk_pos'],
                    'tx_count' => $mainAggregates['masuk_pos_count'],
                    'cash_count' => $mainAggregates['masuk_pos_cash_count'],
                    'qris_count' => $mainAggregates['masuk_pos_qris_count'],
                    'cash_total' => $mainAggregates['masuk_pos_tunai'],
                    'qris_total' => $mainAggregates['masuk_pos_qris'],
                ],
                'catering' => [
                    'total' => $cateringSummary['total'],
                    'order_count' => $cateringSummary['order_count'],
                    'items' => $cateringSummary['items'],
                ],
            ],
        ];
    }

    /**
     * Hitung ringkasan lengkap arus kas untuk periode tertentu (kompatibilitas Commit 1).
     *
     * @param string $period 'day', 'week', 'month', 'year'
     * @param DateTimeInterface|string|null $referenceDate
     * @return array
     */
    public function getSummary(string $period = 'day', DateTimeInterface|string|null $referenceDate = null): array
    {
        $range = CashFlowPeriodHelper::getPeriodRange($period, $referenceDate);

        $mainAggregates = $this->calculatePeriodAggregates($range['start'], $range['end']);
        $compAggregates = $this->calculatePeriodAggregates($range['comparison_start'], $range['comparison_end']);
        $labaPembanding = $compAggregates['laba_bersih'];

        $summary = CashFlowCalculator::calculateSummary(
            $mainAggregates['masuk_pos'],
            $mainAggregates['masuk_katering'],
            $mainAggregates['keluar_pasar_subuh'],
            $mainAggregates['keluar_operasional'],
            $labaPembanding
        );

        return [
            'period' => $range['period'],
            'period_label' => $range['label'],
            'comparison_label' => $range['comparison_label'],
            'start' => $range['start']->toIso8601String(),
            'end' => $range['end']->toIso8601String(),
            'aggregates' => $mainAggregates,
            'comparison_aggregates' => $compAggregates,
            'summary' => $summary,
        ];
    }

    /**
     * Agregasi angka pemasukan dan pengeluaran dalam rentang [start, end).
     */
    public function calculatePeriodAggregates(CarbonInterface $start, CarbonInterface $end): array
    {
        // 1. POS Kasir
        $posTx = TransaksiKasir::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->get();

        $masukPos = 0;
        $masukPosTunai = 0;
        $masukPosQris = 0;
        $cashCount = 0;
        $qrisCount = 0;

        foreach ($posTx as $tx) {
            $val = (int) round((float) $tx->subtotal);
            $masukPos += $val;
            if (strtolower($tx->metode_pembayaran ?: 'tunai') === 'qris') {
                $masukPosQris += $val;
                $qrisCount++;
            } else {
                $masukPosTunai += $val;
                $cashCount++;
            }
        }

        // 2. Katering
        $cateringEntries = $this->fetchCateringEntries($start, $end);
        $masukKatering = (int) $cateringEntries->sum('amount');

        // 3. Expenses
        $expenses = Expense::query()
            ->whereNull('deleted_at')
            ->where('occurred_at', '>=', $start)
            ->where('occurred_at', '<', $end)
            ->get();

        $keluarPasarSubuh = 0;
        $keluarOperasional = 0;

        foreach ($expenses as $exp) {
            $amount = (int) $exp->amount;
            $group = CashFlowCalculator::mapCategoryToGroup($exp->category);

            if ($group === CashFlowCalculator::GROUP_PASAR_SUBUH) {
                $keluarPasarSubuh += $amount;
            } else {
                $keluarOperasional += $amount;
            }
        }

        $totalMasuk = $masukPos + $masukKatering;
        $totalKeluar = $keluarPasarSubuh + $keluarOperasional;
        $labaBersih = $totalMasuk - $totalKeluar;

        return [
            'masuk_pos' => $masukPos,
            'masuk_pos_tunai' => $masukPosTunai,
            'masuk_pos_qris' => $masukPosQris,
            'masuk_pos_count' => $posTx->count(),
            'masuk_pos_cash_count' => $cashCount,
            'masuk_pos_qris_count' => $qrisCount,
            'masuk_katering' => $masukKatering,
            'masuk_katering_count' => $cateringEntries->count(),
            'keluar_pasar_subuh' => $keluarPasarSubuh,
            'keluar_operasional' => $keluarOperasional,
            'keluar_count' => $expenses->count(),
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'laba_bersih' => $labaBersih,
        ];
    }
}
