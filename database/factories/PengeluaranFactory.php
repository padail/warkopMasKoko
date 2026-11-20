<?php

namespace Database\Factories;

use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengeluaran>
 */
class PengeluaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tanggal_pengeluaran' => fake()->date(),
            'kategori' => fake()->randomElement(['Operasional', 'Bahan baku', 'Transportasi']),
            'keterangan' => fake()->sentence(),
            'jumlah' => fake()->numberBetween(1000, 100000),
            'metode_pembayaran' => fake()->randomElement([
                Pengeluaran::METODE_TUNAI,
                Pengeluaran::METODE_NON_TUNAI,
            ]),
        ];
    }
}
