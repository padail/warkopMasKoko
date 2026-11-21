<?php

use App\Filament\Pages\OwnerDashboard;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('dashboard totals separate payment methods and exclude cancelled transactions', function () {
    $employeeA = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $employeeB = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->travelTo('2026-11-21 12:00:00');

    Transaksi::create([
        'user_id' => $employeeA->id,
        'kode_transaksi' => 'DASH-TUNAI',
        'tanggal_transaksi' => '2026-11-21 09:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 120000,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employeeA->id,
        'kode_transaksi' => 'DASH-QRIS',
        'tanggal_transaksi' => '2026-11-21 10:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 80000,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employeeB->id,
        'kode_transaksi' => 'DASH-GOPAY',
        'tanggal_transaksi' => '2026-11-21 11:00:00',
        'metode_pembayaran' => 'gopay',
        'total' => 50000,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employeeB->id,
        'kode_transaksi' => 'DASH-CANCEL',
        'tanggal_transaksi' => '2026-11-21 11:30:00',
        'metode_pembayaran' => 'tunai',
        'total' => 999999,
        'status' => 'batal',
    ]);

    $summary = app(OwnerDashboardService::class)->salesSummary([
        'periode' => OwnerDashboardService::PRESET_HARI_INI,
    ]);

    expect($summary)
        ->transactions_count->toBe(3)
        ->revenue_total->toBe(250000)
        ->revenue_tunai->toBe(120000)
        ->revenue_qris->toBe(80000)
        ->revenue_gopay->toBe(50000);

    $employees = app(OwnerDashboardService::class)->salesByEmployee([
        'periode' => OwnerDashboardService::PRESET_HARI_INI,
    ]);

    expect($employees)->toHaveCount(2);
});

test('dashboard filters today and yesterday separately', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $this->travelTo('2026-11-21 12:00:00');

    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'DASH-TODAY',
        'tanggal_transaksi' => '2026-11-21 09:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 100000,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'DASH-YESTERDAY',
        'tanggal_transaksi' => '2026-11-20 09:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 70000,
        'status' => 'selesai',
    ]);

    $service = app(OwnerDashboardService::class);

    expect($service->salesSummary(['periode' => OwnerDashboardService::PRESET_HARI_INI])['revenue_total'])->toBe(100000);
    expect($service->salesSummary(['periode' => OwnerDashboardService::PRESET_KEMARIN])['revenue_total'])->toBe(70000);

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    Livewire::actingAs($admin)
        ->test(OwnerDashboard::class)
        ->assertSee('DASH-TODAY')
        ->assertDontSee('DASH-YESTERDAY');
});
