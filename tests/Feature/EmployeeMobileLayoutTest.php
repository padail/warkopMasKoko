<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employee mobile navigation is available on all employee work pages', function () {
    $employee = User::factory()->create([
        'role' => User::ROLE_KARYAWAN,
    ]);

    $this->actingAs($employee)
        ->get(route('kasir.index'))
        ->assertOk()
        ->assertSee('id="employee-mobile-nav"', false)
        ->assertSee('Lihat keranjang')
        ->assertSee('id="mobile-cart-bar"', false);

    $this->actingAs($employee)
        ->get(route('stok.index'))
        ->assertOk()
        ->assertSee('id="employee-mobile-nav"', false)
        ->assertSee('Kelola stok')
        ->assertSee('md:hidden', false);

    $this->actingAs($employee)
        ->get(route('karyawan.laporan-harian'))
        ->assertOk()
        ->assertSee('id="employee-mobile-nav"', false)
        ->assertSee('Riwayat pemasukan')
        ->assertSee('md:hidden', false);
});

test('admin pages do not render the employee bottom navigation', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin)
        ->get(route('kasir.index'))
        ->assertOk()
        ->assertDontSee('id="employee-mobile-nav"', false);
});
