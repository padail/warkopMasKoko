<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasir\ProsesPembayaranRequest;
use App\Services\Kasir\TransaksiService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class TransaksiController extends Controller
{
    public function store(ProsesPembayaranRequest $request, TransaksiService $service): JsonResponse
    {
        try {
            $transaction = $service->process(
                $request->user()->id,
                $request->string('metode_pembayaran')->toString(),
                $request->filled('nominal_diterima') ? (int) $request->input('nominal_diterima') : null,
                $request->input('items'),
            );

            return response()->json([
                'message' => 'Transaksi berhasil disimpan.',
                'transaction' => $transaction,
            ], 201);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
