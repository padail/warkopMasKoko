<?php

use App\Filament\Resources\SerahTerimaKas\Pages\ListSerahTerimaKas;
use App\Filament\Resources\SerahTerimaKas\SerahTerimaKasResource;
use App\Models\SerahTerimaKas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can open and verify a pending handover', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $handover = SerahTerimaKas::create([
        'user_id' => $employee->id,
        'tanggal_serah' => '2026-11-21',
        'tunai_harus_disetor' => 100000,
        'tunai_aktual' => 97500,
        'selisih_tunai' => -2500,
        'non_tunai_harus_disetor' => 50000,
        'non_tunai_aktual' => 50000,
        'selisih_non_tunai' => 0,
        'uang_harus_disetor' => 100000,
        'uang_aktual' => 97500,
        'selisih' => -2500,
        'status' => SerahTerimaKas::STATUS_MENUNGGU,
        'catatan' => 'Ada dua lembar rusak.',
        'diserahkan_pada' => '2026-11-21 10:30:00',
    ]);

    Livewire::actingAs($admin)
        ->test(ListSerahTerimaKas::class)
        ->assertTableActionVisible('verifikasi', $handover)
        ->callTableAction('verifikasi', $handover);

    $handover->refresh();

    expect($handover->status)->toBe(SerahTerimaKas::STATUS_TERVERIFIKASI);
    expect($handover->diterima_oleh)->toBe($admin->id);
    expect($handover->diverifikasi_pada)->not->toBeNull();
});

test('verified handover cannot be verified again', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $handover = SerahTerimaKas::create([
        'user_id' => $employee->id,
        'tanggal_serah' => '2026-11-21',
        'uang_harus_disetor' => 100000,
        'uang_aktual' => 100000,
        'selisih' => 0,
        'status' => SerahTerimaKas::STATUS_TERVERIFIKASI,
        'diterima_oleh' => $admin->id,
        'diverifikasi_pada' => '2026-11-21 10:30:00',
    ]);

    Livewire::actingAs($admin)
        ->test(ListSerahTerimaKas::class)
        ->assertTableActionHidden('verifikasi', $handover);

    $handover->refresh();
    expect($handover->status)->toBe(SerahTerimaKas::STATUS_TERVERIFIKASI);
});

test('employee cannot use verification action', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $handover = SerahTerimaKas::create([
        'user_id' => $employee->id,
        'tanggal_serah' => '2026-11-21',
        'uang_harus_disetor' => 100000,
        'uang_aktual' => 100000,
        'selisih' => 0,
        'status' => SerahTerimaKas::STATUS_MENUNGGU,
    ]);

    $this->actingAs($employee)
        ->get(SerahTerimaKasResource::getUrl())
        ->assertForbidden();

    $handover->refresh();
    expect($handover->status)->toBe(SerahTerimaKas::STATUS_MENUNGGU);
});
