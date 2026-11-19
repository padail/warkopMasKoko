<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun ADMIN / OWNER
        User::updateOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'owner@warkop.test')],
            [
                'name' => 'Owner Warkop',
                'password' => Hash::make(
                    env('SEED_ADMIN_PASSWORD', '@Owner123')
                ),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Akun KARYAWAN / KASIR
        User::updateOrCreate(
            ['email' => env('SEED_KARYAWAN_EMAIL', 'kasir@warkop.test')],
            [
                'name' => 'Karyawan Warkop',
                'password' => Hash::make(
                    env('SEED_KARYAWAN_PASSWORD', '@Karyawan123')
                ),
                'role' => User::ROLE_KARYAWAN,
                'email_verified_at' => now(),
            ]
        );
    }
}
