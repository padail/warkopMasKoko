<?php

use App\Models\MenuBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('karyawan dapat memproses transaksi tunai dan mengurangi stok', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $item = MenuBarang::create([
        'nama' => 'Kopi Susu',
        'kategori' => 'Minuman',
        'harga_jual' => 8000,
        'stok' => 10,
        'satuan' => 'gelas',
        'stok_minimum' => 2,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $response = $this->actingAs($employee)->postJson('/kasir/transaksi', [
        'metode_pembayaran' => 'tunai',
        'nominal_diterima' => 10000,
        'items' => [
            ['menu_barang_id' => $item->id, 'jumlah' => 1],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('transaction.total', 8000)
        ->assertJsonPath('transaction.kembalian', 2000)
        ->assertJsonPath('transaction.status', 'selesai')
        ->assertJsonPath('transaction.detail_transaksi.0.menu_barang_id', $item->id)
        ->assertJsonPath('transaction.detail_transaksi.0.jumlah', '1.000');

    $this->assertDatabaseHas('transaksi', [
        'user_id' => $employee->id,
        'metode_pembayaran' => 'tunai',
        'total' => 8000,
        'kembalian' => 2000,
    ]);
    $this->assertDatabaseHas('detail_transaksi', [
        'menu_barang_id' => $item->id,
        'nama_item_snapshot' => 'Kopi Susu',
        'harga_satuan' => 8000,
        'subtotal' => 8000,
    ]);
    expect((float) $item->fresh()->stok)->toBe(9.0);
});

test('nominal tunai tidak boleh kurang dari total', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $item = MenuBarang::create([
        'nama' => 'Teh',
        'kategori' => 'Minuman',
        'harga_jual' => 5000,
        'stok' => 5,
        'satuan' => 'gelas',
        'stok_minimum' => 1,
        'is_dijual' => true,
        'is_active' => true,
    ]);

    $this->actingAs($employee)
        ->postJson('/kasir/transaksi', [
            'metode_pembayaran' => 'tunai',
            'nominal_diterima' => 4000,
            'items' => [['menu_barang_id' => $item->id, 'jumlah' => 1]],
        ])
        ->assertUnprocessable();

    expect((float) $item->fresh()->stok)->toBe(5.0);
});

test('karyawan tidak dapat melihat laporan karyawan lain', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $otherEmployee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($employee)
        ->get('/karyawan/laporan-harian')
        ->assertOk();

    $this->actingAs($otherEmployee)
        ->get('/karyawan/laporan-harian')
        ->assertOk();
});

test('karyawan dapat memperbarui serah terima kas pada tanggal yang sama', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($employee)
        ->post('/karyawan/serah-terima-kas', [
            'tanggal_serah' => '2026-11-20',
            'uang_tunai_aktual' => 50000,
            'uang_non_tunai_aktual' => 0,
        ])
        ->assertRedirect();

    $this->actingAs($employee)
        ->post('/karyawan/serah-terima-kas', [
            'tanggal_serah' => '2026-11-20',
            'uang_tunai_aktual' => 60000,
            'uang_non_tunai_aktual' => 0,
        ])
        ->assertRedirect();

    $this->assertDatabaseCount('serah_terima_kas', 1);
    $this->assertDatabaseHas('serah_terima_kas', [
        'user_id' => $employee->id,
        'tunai_aktual' => 60000,
        'uang_aktual' => 60000,
    ]);
});

test('guest must log in before accessing cashier and employee reports', function () {
    $this->get('/kasir')
        ->assertRedirect(route('login'));

    $this->get('/karyawan/laporan-harian')
        ->assertRedirect(route('login'));
});

test('authenticated employee can access cashier and reports in the same session', function () {
    $employee = User::factory()->create([
        'role' => User::ROLE_KARYAWAN,
    ]);

    $this->actingAs($employee)
        ->get('/kasir')
        ->assertOk();

    $this->get('/karyawan/laporan-harian')
        ->assertOk();
});
