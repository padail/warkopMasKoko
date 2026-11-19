<?php

namespace Database\Seeders;

use App\Models\MenuBarang;
use Illuminate\Database\Seeder;

class MenuBarangSeeder extends Seeder
{
    public function run(): void
    {
        $barang = [
            [
                'nama' => 'Kopi Hitam',
                'kategori' => 'Minuman',
                'harga_jual' => 5000,
                'stok' => 30,
                'satuan' => 'gelas',
                'stok_minimum' => 5,
                'is_dijual' => true,
                'is_active' => true,
            ],
            [
                'nama' => 'Es Teh',
                'kategori' => 'Minuman',
                'harga_jual' => 5000,
                'stok' => 25,
                'satuan' => 'gelas',
                'stok_minimum' => 5,
                'is_dijual' => true,
                'is_active' => true,
            ],
            [
                'nama' => 'Mie Instan',
                'kategori' => 'Makanan',
                'harga_jual' => 10000,
                'stok' => 20,
                'satuan' => 'pcs',
                'stok_minimum' => 5,
                'is_dijual' => true,
                'is_active' => true,
            ],
            [
                'nama' => 'Air Mineral',
                'kategori' => 'Minuman',
                'harga_jual' => 4000,
                'stok' => 24,
                'satuan' => 'botol',
                'stok_minimum' => 6,
                'is_dijual' => true,
                'is_active' => true,
            ],
            [
                'nama' => 'Gula Pasir',
                'kategori' => 'Bahan Baku',
                'harga_jual' => 0,
                'stok' => 3.5,
                'satuan' => 'kg',
                'stok_minimum' => 1,
                'is_dijual' => false,
                'is_active' => true,
            ],
        ];

        foreach ($barang as $item) {
            MenuBarang::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}

