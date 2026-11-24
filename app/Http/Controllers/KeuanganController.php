<?php

namespace App\Http\Controllers;

use App\Services\Keuangan\CashFlowService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    public function __construct(
        protected CashFlowService $cashFlowService
    ) {}

    /**
     * Halaman Utama Laporan Arus Kas (Keuangan)
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', 'day');
        $date = $request->query('date');

        return view('keuangan.index', [
            'initialPeriod' => $period,
            'initialDate' => $date,
        ]);
    }
}
