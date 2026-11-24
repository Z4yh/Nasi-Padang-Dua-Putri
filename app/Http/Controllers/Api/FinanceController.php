<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\Keuangan\CashFlowCalculator;
use App\Services\Keuangan\CashFlowPeriodHelper;
use App\Services\Keuangan\CashFlowService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    public function __construct(
        protected CashFlowService $cashFlowService
    ) {}

    /**
     * GET /api/finance/summary
     * Mengembalikan ringkasan laporan arus kas per periode.
     */
    public function summary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => ['nullable', 'string', 'in:day,week,month,year'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ], [
            'period.in' => 'Periode harus salah satu dari: day, week, month, year.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter permintaan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $period = $request->query('period', 'day');
        $date = $request->query('date');

        $summary = $this->cashFlowService->getFinanceSummary($period, $date);

        return response()->json($summary, 200);
    }

    /**
     * GET /api/finance/entries
     * Mengembalikan riwayat entri arus kas dengan filter dan pagination cursor.
     */
    public function entries(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => ['nullable', 'string', 'in:day,week,month,year'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'string', 'in:all,in,out'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'cursor' => ['nullable', 'string'],
        ], [
            'period.in' => 'Periode harus salah satu dari: day, week, month, year.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'type.in' => 'Tipe entri harus salah satu dari: all, in, out.',
            'limit.integer' => 'Batas limit harus berupa angka bulat.',
            'limit.min' => 'Limit minimal 1.',
            'limit.max' => 'Limit maksimal 50.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter permintaan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $period = $request->query('period', 'day');
        $date = $request->query('date');
        $type = $request->query('type', 'all');
        $limit = (int) $request->query('limit', 20);
        $cursor = $request->query('cursor');

        $range = CashFlowPeriodHelper::getPeriodRange($period, $date);

        $result = $this->cashFlowService->getPaginatedEntries(
            $range['start'],
            $range['end'],
            $type,
            $cursor,
            $limit
        );

        return response()->json($result, 200);
    }

    /**
     * POST /api/finance/expenses
     * Mencatat pengeluaran operasional baru.
     */
    public function storeExpense(StoreExpenseRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $occurredAt = isset($validated['occurred_at'])
            ? Carbon::parse($validated['occurred_at'])->setTimezone(CashFlowPeriodHelper::TIMEZONE)
            : Carbon::now(CashFlowPeriodHelper::TIMEZONE);

        $expense = Expense::create([
            'category' => $validated['category'],
            'amount' => (int) $validated['amount'],
            'note' => $validated['note'] ?? null,
            'fund_source' => $validated['fund_source'],
            'occurred_at' => $occurredAt,
            'attachment_url' => null,
            'created_by' => $request->user()->id_user,
        ]);

        return response()->json($expense, 201);
    }

    /**
     * PATCH /api/finance/expenses/:id
     * Mengubah rincian pengeluaran sebagian.
     */
    public function updateExpense(UpdateExpenseRequest $request, int|string $id): JsonResponse
    {
        $expense = Expense::whereNull('deleted_at')->find($id);

        if (! $expense) {
            return response()->json([
                'message' => 'Pengeluaran tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validated();

        if (array_key_exists('category', $validated)) {
            $expense->category = $validated['category'];
        }
        if (array_key_exists('amount', $validated)) {
            $expense->amount = (int) $validated['amount'];
        }
        if (array_key_exists('note', $validated)) {
            $expense->note = $validated['note'];
        }
        if (array_key_exists('fund_source', $validated)) {
            $expense->fund_source = $validated['fund_source'];
        }
        if (array_key_exists('occurred_at', $validated)) {
            $expense->occurred_at = $validated['occurred_at']
                ? Carbon::parse($validated['occurred_at'])->setTimezone(CashFlowPeriodHelper::TIMEZONE)
                : $expense->occurred_at;
        }

        $expense->save();

        return response()->json($expense, 200);
    }

    /**
     * DELETE /api/finance/expenses/:id
     * Menghapus pengeluaran (soft delete).
     */
    public function destroyExpense(Request $request, int|string $id): Response|JsonResponse
    {
        $expense = Expense::whereNull('deleted_at')->find($id);

        if (! $expense) {
            return response()->json([
                'message' => 'Pengeluaran tidak ditemukan.',
            ], 404);
        }

        $expense->delete();

        return response()->noContent();
    }

    /**
     * GET /api/finance/export
     * Ekspor laporan arus kas ke format PDF atau Excel CSV.
     */
    public function export(Request $request): StreamedResponse|View|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'format' => ['required', 'string', 'in:pdf,csv'],
            'period' => ['nullable', 'string', 'in:day,week,month,year'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'string', 'in:all,in,out'],
        ], [
            'format.required' => 'Format ekspor wajib dipilih (pdf atau csv).',
            'format.in' => 'Format ekspor harus berupa pdf atau csv.',
            'period.in' => 'Periode harus salah satu dari: day, week, month, year.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'type.in' => 'Tipe entri harus salah satu dari: all, in, out.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter permintaan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $format = $request->query('format');
        $period = $request->query('period', 'day');
        $date = $request->query('date');
        $type = $request->query('type', 'all');

        $range = CashFlowPeriodHelper::getPeriodRange($period, $date);
        $dateString = $range['start']->format('Ymd');
        $typeFilter = ($type === 'all') ? null : $type;

        $entries = $this->cashFlowService->getEntries($range['start'], $range['end'], $typeFilter);
        $summary = $this->cashFlowService->getFinanceSummary($period, $date);

        if ($format === 'csv') {
            $filename = "laporan-arus-kas_{$period}_{$dateString}.csv";

            return response()->streamDownload(function () use ($entries) {
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM untuk Microsoft Excel
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($handle, [
                    'Tanggal',
                    'Jam',
                    'Jenis',
                    'Sumber',
                    'Kategori',
                    'Keterangan',
                    'Metode / Sumber Dana',
                    'Nominal',
                    'Referensi',
                ]);

                if ($entries->isEmpty()) {
                    fputcsv($handle, [
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        'Tidak ada transaksi',
                        '-',
                        0,
                        '-',
                    ]);
                } else {
                    foreach ($entries as $item) {
                        $occurredAt = $item['occurred_at'];
                        $categoryLabel = $item['category'] ? CashFlowCalculator::getCategoryLabel($item['category']) : '-';
                        $typeLabel = $item['type'] === 'in' ? 'Masuk' : 'Keluar';
                        $sourceLabel = match ($item['source']) {
                            'pos' => 'Kasir POS',
                            'catering' => 'Katering',
                            'expense' => 'Pengeluaran',
                            default => ucfirst($item['source']),
                        };

                        $subtitle = $item['subtitle'] ? ' (' . $item['subtitle'] . ')' : '';

                        fputcsv($handle, [
                            $this->sanitizeCsvCell($occurredAt->format('Y-m-d')),
                            $this->sanitizeCsvCell($occurredAt->format('H:i')),
                            $this->sanitizeCsvCell($typeLabel),
                            $this->sanitizeCsvCell($sourceLabel),
                            $this->sanitizeCsvCell($categoryLabel),
                            $this->sanitizeCsvCell($item['title'] . $subtitle),
                            $this->sanitizeCsvCell(strtoupper($item['method'])),
                            (int) $item['amount'],
                            $this->sanitizeCsvCell((string) $item['ref_id']),
                        ]);
                    }
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // Format PDF -> Tampilan cetak A4 teroptimasi print-to-PDF
        return view('keuangan.pdf', [
            'summary' => $summary,
            'entries' => $entries,
            'period' => $period,
            'range' => $range,
            'type' => $type,
            'printedAt' => Carbon::now(CashFlowPeriodHelper::TIMEZONE),
        ]);
    }

    /**
     * Perlindungan dari CSV Injection:
     * Awali nilai yang diawali tanda =, +, -, atau @ dengan tanda petik tunggal aman.
     */
    protected function sanitizeCsvCell(mixed $val): string
    {
        $str = (string) $val;
        if ($str !== '' && in_array($str[0], ['=', '+', '-', '@'], true)) {
            return "'" . $str;
        }
        return $str;
    }
}
