<?php

use App\Models\MenuBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows karyawan to create, update details, deactivate items, and update stock', function (): void {
    $karyawan = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($karyawan)
        ->post(route('stok.store'), [
            'nama' => 'Milo',
            'kategori' => 'Minuman',
            'harga_jual' => 6000,
            'stok' => 12,
            'satuan' => 'pcs',
            'stok_minimum' => 4,
            'is_dijual' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('stok.index'));

    $item = MenuBarang::where('nama', 'Milo')->firstOrFail();

    $this->actingAs($karyawan)
        ->get(route('stok.edit', $item))
        ->assertOk()
        ->assertSeeText('Edit Identitas & Harga Barang');

    $this->actingAs($karyawan)
        ->put(route('stok.update', $item), [
            'nama' => 'Milo Susu',
            'kategori' => 'Minuman',
            'harga_jual' => 7000,
            'stok' => 12,
            'satuan' => 'pcs',
            'stok_minimum' => 4,
            'is_dijual' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('stok.index'));

    expect($item->fresh()->nama)->toBe('Milo Susu')
        ->and($item->fresh()->harga_jual)->toBe(7000);

    $this->actingAs($karyawan)
        ->patch(route('stok.toggle-active', $item))
        ->assertRedirect(route('stok.index'));

    expect($item->fresh()->is_active)->toBeFalse();

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
        ->and($item->harga_jual)->toBe(7000)
        ->and($item->nama)->toBe('Milo Susu');
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
