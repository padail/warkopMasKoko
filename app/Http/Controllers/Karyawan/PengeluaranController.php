<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_pengeluaran' => ['required', 'date_format:Y-m-d'],
            'kategori' => ['required', 'string', 'max:80'],
            'keterangan' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'metode_pembayaran' => ['required', 'in:tunai,non_tunai'],
            'filter_tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);

        Pengeluaran::create([
            'user_id' => $request->user()->id,
            'tanggal_pengeluaran' => $validated['tanggal_pengeluaran'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
        ]);

        $filters = isset($validated['filter_tanggal'])
            ? ['tanggal' => $validated['filter_tanggal']]
            : [];

        return redirect()
            ->route('karyawan.laporan-harian', $filters)
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }
}
