<?php

use App\Models\MenuBarang;
use App\Models\User;
use App\Services\Kasir\TransaksiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates stock to a positive value or zero and records actor and reason', function (): void {
    $karyawan = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $item = MenuBarang::create([
        'nama' => 'Kopi Susu',
        'kategori' => 'Minuman',
        'harga_jual' => 7000,
        'stok' => 10,
        'satuan' => 'gelas',
        'stok_minimum' => 5,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $this->actingAs($karyawan)
        ->patch(route('stok.update-stok', $item), [
            'mode' => 'set',
            'nilai' => 15,
            'alasan' => 'Barang masuk',
        ])
        ->assertRedirect(route('stok.index'));

    $item->refresh();
    expect((float) $item->stok)->toBe(15.0)
        ->and($item->stok_diperbarui_oleh)->toBe($karyawan->id)
        ->and($item->alasan_perubahan_stok)->toBe('Barang masuk');

    $this->actingAs($karyawan)
        ->patch(route('stok.update-stok', $item), [
            'mode' => 'set',
            'nilai' => 0,
            'alasan' => 'Hasil pemeriksaan stok',
        ])
        ->assertRedirect(route('stok.index'));

    expect((float) $item->fresh()->stok)->toBe(0.0);
});

it('rejects negative stock values and stays consistent with kasir transactions', function (): void {
    $karyawan = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $item = MenuBarang::create([
        'nama' => 'Es Teh',
        'kategori' => 'Minuman',
        'harga_jual' => 5000,
        'stok' => 10,
        'satuan' => 'gelas',
        'stok_minimum' => 3,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $this->actingAs($karyawan)
        ->patch(route('stok.update-stok', $item), [
            'mode' => 'set',
            'nilai' => -5,
            'alasan' => 'Koreksi pencatatan',
        ])
        ->assertSessionHasErrors('nilai');

    expect((float) $item->fresh()->stok)->toBe(10.0);

    // Pastikan integrasi dengan transaksi kasir tetap konsisten
    app(TransaksiService::class)->process($karyawan->id, 'tunai', 10000, [
        ['menu_barang_id' => $item->id, 'jumlah' => 2],
    ]);

    expect((float) $item->fresh()->stok)->toBe(8.0);
});
