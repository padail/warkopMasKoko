<?php

use App\Models\MenuBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prevents karyawan from changing item price, identity, or status even via direct request', function (): void {
    $karyawan = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $item = MenuBarang::create([
        'nama' => 'Milo',
        'kategori' => 'Minuman',
        'harga_jual' => 6000,
        'stok' => 12,
        'satuan' => 'pcs',
        'stok_minimum' => 4,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    // Karyawan tidak bisa hit endpoint update detail (403)
    $this->actingAs($karyawan)
        ->put(route('stok.update', $item), [
            'nama' => 'Milo Ilegal',
            'kategori' => 'Snack',
            'harga_jual' => 1000,
            'stok' => 12,
            'satuan' => 'pcs',
            'stok_minimum' => 4,
            'is_dijual' => true,
            'is_active' => true,
        ])
        ->assertForbidden();

    // Karyawan tidak bisa menonaktifkan barang (403)
    $this->actingAs($karyawan)
        ->patch(route('stok.toggle-active', $item))
        ->assertForbidden();

    // Meski karyawan menyisipkan harga_jual ke endpoint update-stok, harga tidak berubah
    $this->actingAs($karyawan)
        ->patch(route('stok.update-stok', $item), [
            'mode' => 'set',
            'nilai' => 20,
            'alasan' => 'Barang masuk',
            'harga_jual' => 999,
            'nama' => 'Nama Diubah Paksa',
        ])
        ->assertRedirect(route('stok.index'));

    $item->refresh();
    expect((float) $item->stok)->toBe(20.0)
        ->and($item->harga_jual)->toBe(6000)
        ->and($item->nama)->toBe('Milo');
});

it('allows admin to create, update details, and deactivate items', function (): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->post(route('stok.store'), [
            'nama' => 'Chiki Balls',
            'kategori' => 'Snack',
            'harga_jual' => 3000,
            'stok' => 15,
            'satuan' => 'pcs',
            'stok_minimum' => 5,
            'is_dijual' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('stok.index'));

    $item = MenuBarang::where('nama', 'Chiki Balls')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('stok.update', $item), [
            'nama' => 'Chiki Balls Keju',
            'kategori' => 'Snack',
            'harga_jual' => 3500,
            'stok' => 15,
            'satuan' => 'pcs',
            'stok_minimum' => 5,
            'is_dijual' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('stok.index'));

    expect($item->fresh()->harga_jual)->toBe(3500);

    $this->actingAs($admin)
        ->patch(route('stok.toggle-active', $item))
        ->assertRedirect(route('stok.index'));

    expect($item->fresh()->is_active)->toBeFalse();
});
