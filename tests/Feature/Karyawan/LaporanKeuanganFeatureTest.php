<?php

use App\Models\Pengeluaran;
use App\Models\SerahTerimaKas;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employee can record a categorized expense with payment method', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $response = $this->actingAs($employee)->post('/karyawan/pengeluaran', [
        'tanggal_pengeluaran' => '2026-11-20',
        'kategori' => 'Bahan baku',
        'keterangan' => 'Membeli gula dan susu',
        'jumlah' => 25000,
        'metode_pembayaran' => 'non_tunai',
        'filter_tanggal' => '',
    ]);

    $response->assertRedirect(route('karyawan.laporan-harian'));
    $this->assertDatabaseHas('pengeluaran', [
        'user_id' => $employee->id,
        'kategori' => 'Bahan baku',
        'keterangan' => 'Membeli gula dan susu',
        'jumlah' => 25000,
        'metode_pembayaran' => 'non_tunai',
    ]);
});

test('employee cannot record an expense with a zero amount', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($employee)
        ->from('/karyawan/laporan-harian')
        ->post('/karyawan/pengeluaran', [
            'tanggal_pengeluaran' => '2026-11-20',
            'kategori' => 'Bahan baku',
            'keterangan' => 'Membeli gula',
            'jumlah' => 0,
            'metode_pembayaran' => 'tunai',
            'filter_tanggal' => '',
        ])
        ->assertRedirect('/karyawan/laporan-harian')
        ->assertSessionHasErrors('jumlah');

    $this->assertDatabaseCount('pengeluaran', 0);
});

test('all dates report totals only the signed-in employee cash and non-cash activity', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    $otherEmployee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-EMP-CASH',
        'tanggal_transaksi' => '2026-11-20 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 10000,
        'nominal_diterima' => 10000,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-EMP-QRIS',
        'tanggal_transaksi' => '2026-11-19 10:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 7000,
        'nominal_diterima' => null,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $otherEmployee->id,
        'kode_transaksi' => 'RPT-OTHER',
        'tanggal_transaksi' => '2026-11-20 11:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 90000,
        'nominal_diterima' => 90000,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);

    Pengeluaran::factory()->for($employee)->create([
        'tanggal_pengeluaran' => '2026-11-20',
        'kategori' => 'Operasional',
        'jumlah' => 3000,
        'metode_pembayaran' => 'tunai',
    ]);
    Pengeluaran::factory()->for($employee)->create([
        'tanggal_pengeluaran' => '2026-11-19',
        'kategori' => 'Bahan baku',
        'jumlah' => 1000,
        'metode_pembayaran' => 'non_tunai',
    ]);
    Pengeluaran::factory()->for($otherEmployee)->create([
        'tanggal_pengeluaran' => '2026-11-20',
        'jumlah' => 80000,
    ]);

    $response = $this->actingAs($employee)->get('/karyawan/laporan-harian');

    $response->assertOk()
        ->assertSee('Ringkasan semua tanggal')
        ->assertSee('Rp 17.000')
        ->assertSee('Rp 4.000')
        ->assertSee('Rp 13.000')
        ->assertSee('RPT-EMP-CASH')
        ->assertSee('RPT-EMP-QRIS')
        ->assertDontSee('RPT-OTHER');
});

test('selected date filters ledger records and summary', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-TODAY',
        'tanggal_transaksi' => '2026-11-20 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 10000,
        'nominal_diterima' => 10000,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-YESTERDAY',
        'tanggal_transaksi' => '2026-11-19 10:00:00',
        'metode_pembayaran' => 'gopay',
        'total' => 7000,
        'nominal_diterima' => null,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Pengeluaran::factory()->for($employee)->create([
        'tanggal_pengeluaran' => '2026-11-19',
        'jumlah' => 1000,
        'metode_pembayaran' => 'non_tunai',
    ]);

    $response = $this->actingAs($employee)
        ->get('/karyawan/laporan-harian?tanggal=2026-11-19');

    $response->assertOk()
        ->assertSee('RPT-YESTERDAY')
        ->assertDontSee('RPT-TODAY')
        ->assertSee('Rp 7.000')
        ->assertSee('Rp 1.000')
        ->assertSee('Rp 6.000');
});

test('cash handover stores separate net cash and non-cash amounts', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-HO-CASH',
        'tanggal_transaksi' => '2026-11-20 10:00:00',
        'metode_pembayaran' => 'tunai',
        'total' => 10000,
        'nominal_diterima' => 10000,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Transaksi::create([
        'user_id' => $employee->id,
        'kode_transaksi' => 'RPT-HO-QRIS',
        'tanggal_transaksi' => '2026-11-20 11:00:00',
        'metode_pembayaran' => 'qris',
        'total' => 6000,
        'nominal_diterima' => null,
        'kembalian' => 0,
        'status' => 'selesai',
    ]);
    Pengeluaran::factory()->for($employee)->create([
        'tanggal_pengeluaran' => '2026-11-20',
        'jumlah' => 2000,
        'metode_pembayaran' => 'tunai',
    ]);
    Pengeluaran::factory()->for($employee)->create([
        'tanggal_pengeluaran' => '2026-11-20',
        'jumlah' => 1000,
        'metode_pembayaran' => 'non_tunai',
    ]);

    $response = $this->actingAs($employee)->post('/karyawan/serah-terima-kas', [
        'tanggal_serah' => '2026-11-20',
        'uang_tunai_aktual' => 7500,
        'uang_non_tunai_aktual' => 5000,
    ]);

    $response->assertRedirect(route('karyawan.laporan-harian', ['tanggal' => '2026-11-20']));
    $this->assertDatabaseCount('serah_terima_kas', 1);
    $this->assertDatabaseHas('serah_terima_kas', [
        'user_id' => $employee->id,
        'tunai_harus_disetor' => 8000,
        'tunai_aktual' => 7500,
        'selisih_tunai' => -500,
        'non_tunai_harus_disetor' => 5000,
        'non_tunai_aktual' => 5000,
        'selisih_non_tunai' => 0,
        'uang_harus_disetor' => 8000,
        'uang_aktual' => 7500,
        'selisih' => -500,
    ]);
    expect(SerahTerimaKas::query()->first()->tanggal_serah->toDateString())->toBe('2026-11-20');
});
