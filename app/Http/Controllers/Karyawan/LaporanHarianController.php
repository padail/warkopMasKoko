<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\SerahTerimaKas;
use App\Models\Transaksi;
use App\Services\Karyawan\RingkasanKeuanganService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanHarianController extends Controller
{
    public function index(Request $request, RingkasanKeuanganService $summaryService): View
    {
        $validated = $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $date = $validated['tanggal'] ?? null;

        $transactionsQuery = Transaksi::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'selesai');

        if ($date !== null) {
            $transactionsQuery->whereDate('tanggal_transaksi', $date);
        }

        $expensesQuery = Pengeluaran::query()
            ->where('user_id', $request->user()->id);

        if ($date !== null) {
            $expensesQuery->whereDate('tanggal_pengeluaran', $date);
        }

        $transactions = $transactionsQuery
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'transaksi_page')
            ->withQueryString();

        $expenses = $expensesQuery
            ->orderByDesc('tanggal_pengeluaran')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'pengeluaran_page')
            ->withQueryString();

        $summary = $summaryService->forEmployee($request->user()->id, $date);
        $handover = $date === null
            ? null
            : SerahTerimaKas::query()
                ->where('user_id', $request->user()->id)
                ->whereDate('tanggal_serah', $date)
                ->first();

        return view('karyawan.laporan-harian.index', [
            'date' => $date,
            'summary' => $summary,
            'transactions' => $transactions,
            'expenses' => $expenses,
            'handover' => $handover,
        ]);
    }
}
