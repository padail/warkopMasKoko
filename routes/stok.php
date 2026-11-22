<?php

use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,karyawan'])
    ->prefix('stok')
    ->name('stok.')
    ->group(function (): void {
        Route::get('/', [StokController::class, 'index'])->name('index');
        Route::get('/{menuBarang}/edit', [StokController::class, 'edit'])->name('edit');
        Route::patch('/{menuBarang}/stok', [StokController::class, 'updateStok'])->name('update-stok');

        Route::middleware(['role:admin'])->group(function (): void {
            Route::post('/', [StokController::class, 'store'])->name('store');
            Route::put('/{menuBarang}', [StokController::class, 'update'])->name('update');
            Route::patch('/{menuBarang}/toggle-active', [StokController::class, 'toggleActive'])->name('toggle-active');
        });
    });
