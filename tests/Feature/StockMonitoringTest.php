<?php

use App\Filament\Resources\MenuBarangs\MenuBarangResource;
use App\Models\MenuBarang;
use App\Models\User;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('stock summary separates out, low, and safe active items', function () {
    MenuBarang::create([
        'nama' => 'Susu',
        'kategori' => 'Bahan',
        'harga_jual' => 5000,
        'stok' => 0,
        'satuan' => 'pcs',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);
    MenuBarang::create([
        'nama' => 'Gula',
        'kategori' => 'Bahan',
        'harga_jual' => 3000,
        'stok' => 3,
        'satuan' => 'kg',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);
    MenuBarang::create([
        'nama' => 'Kopi',
        'kategori' => 'Bahan',
        'harga_jual' => 8000,
        'stok' => 20,
        'satuan' => 'kg',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);
    MenuBarang::create([
        'nama' => 'Barang Lama',
        'kategori' => 'Lainnya',
        'harga_jual' => 1000,
        'stok' => 1,
        'satuan' => 'pcs',
        'stok_minimum' => 0,
        'is_dijual' => false,
        'is_active' => false,
    ]);

    expect(app(OwnerDashboardService::class)->stockSummary())->toBe([
        'active_count' => 3,
        'sold_count' => 3,
        'out_count' => 1,
        'low_count' => 1,
        'safe_count' => 1,
    ]);
});

test('admin can open stock monitoring resource', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    MenuBarang::create([
        'nama' => 'Kopi',
        'kategori' => 'Bahan',
        'harga_jual' => 8000,
        'stok' => 20,
        'satuan' => 'kg',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(MenuBarangResource::getUrl())
        ->assertOk()
        ->assertSee('Kopi');
});

test('employee is rejected from stock monitoring resource', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($employee)
        ->get(MenuBarangResource::getUrl())
        ->assertForbidden();
});
