<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can open filament panel', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('employee is rejected from filament panel', function () {
    $employee = User::factory()->create(['role' => User::ROLE_KARYAWAN]);

    $this->actingAs($employee)
        ->get('/admin')
        ->assertForbidden();
});

test('guest is redirected to filament login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});
