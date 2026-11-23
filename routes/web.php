<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Karyawan\LaporanHarianController;
use App\Http\Controllers\Karyawan\PengeluaranController;
use App\Http\Controllers\Karyawan\SerahTerimaKasController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Kasir\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->guard()->check()
        ? redirect()->route('kasir.index')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/transaksi', [TransaksiController::class, 'store'])->name('kasir.transaksi.store');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/laporan-harian', [LaporanHarianController::class, 'index'])
        ->name('karyawan.laporan-harian');
    Route::post('/karyawan/pengeluaran', [PengeluaranController::class, 'store'])
        ->name('karyawan.pengeluaran.store');
    Route::post('/karyawan/serah-terima-kas', [SerahTerimaKasController::class, 'store'])
        ->name('karyawan.serah-terima-kas.store');
});

require __DIR__.'/stok.php';
