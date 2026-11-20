<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\SerahTerimaKas;
use App\Services\Karyawan\RingkasanKeuanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SerahTerimaKasController extends Controller
{
    public function store(Request $request, RingkasanKeuanganService $summaryService): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_serah' => ['required', 'date_format:Y-m-d'],
            'uang_tunai_aktual' => ['required', 'integer', 'min:0'],
            'uang_non_tunai_aktual' => ['required', 'integer', 'min:0'],
        ]);

        $date = $validated['tanggal_serah'];
        $summary = $summaryService->forEmployee($request->user()->id, $date);
        $actualCash = (int) $validated['uang_tunai_aktual'];
        $actualNonCash = (int) $validated['uang_non_tunai_aktual'];
        $cashDifference = $actualCash - $summary['deposit_cash'];
        $nonCashDifference = $actualNonCash - $summary['deposit_non_cash'];

        DB::transaction(function () use (
            $request,
            $date,
            $summary,
            $actualCash,
            $actualNonCash,
            $cashDifference,
            $nonCashDifference,
        ): void {
            $handover = SerahTerimaKas::query()
                ->where('user_id', $request->user()->id)
                ->whereDate('tanggal_serah', $date)
                ->lockForUpdate()
                ->first();

            $values = [
                'tunai_harus_disetor' => $summary['deposit_cash'],
                'tunai_aktual' => $actualCash,
                'selisih_tunai' => $cashDifference,
                'non_tunai_harus_disetor' => $summary['deposit_non_cash'],
                'non_tunai_aktual' => $actualNonCash,
                'selisih_non_tunai' => $nonCashDifference,
                'uang_harus_disetor' => $summary['deposit_cash'],
                'uang_aktual' => $actualCash,
                'selisih' => $cashDifference,
                'status' => 'menunggu',
            ];

            if ($handover) {
                $handover->update($values);
            } else {
                SerahTerimaKas::create([
                    'user_id' => $request->user()->id,
                    'tanggal_serah' => $date,
                    ...$values,
                ]);
            }
        });

        return redirect()
            ->route('karyawan.laporan-harian', ['tanggal' => $date])
            ->with('success', 'Serah terima tunai dan non-tunai berhasil dikirim.');
    }
}
