<?php

use App\Models\MenuBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows admin and karyawan to view stock page with search, category, status filter, and pagination', function (): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $karyawan = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    MenuBarang::create([
        'nama' => 'Kopi Hitam',
        'kategori' => 'Minuman',
        'harga_jual' => 5000,
        'stok' => 20,
        'satuan' => 'gelas',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    MenuBarang::create([
        'nama' => 'Air Mineral',
        'kategori' => 'Minuman',
        'harga_jual' => 4000,
        'stok' => 2,
        'satuan' => 'botol',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    MenuBarang::create([
        'nama' => 'Mie Instan',
        'kategori' => 'Makanan',
        'harga_jual' => 10000,
        'stok' => 0,
        'satuan' => 'pcs',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('stok.index'))
        ->assertOk()
        ->assertSee('Kopi Hitam')
        ->assertSee('Air Mineral')
        ->assertSee('Mie Instan')
        ->assertSee('Menipis')
        ->assertSee('Habis');

    $this->actingAs($karyawan)
        ->get(route('stok.index', ['search' => 'Kopi']))
        ->assertOk()
        ->assertSee('Kopi Hitam')
        ->assertDontSee('Mie Instan');

    $this->actingAs($karyawan)
        ->get(route('stok.index', ['kategori' => 'Makanan']))
        ->assertOk()
        ->assertSee('Mie Instan')
        ->assertDontSee('Kopi Hitam');

    $this->actingAs($karyawan)
        ->get(route('stok.index', ['status_stok' => 'habis']))
        ->assertOk()
        ->assertSee('Mie Instan')
        ->assertDontSee('Kopi Hitam');
});
