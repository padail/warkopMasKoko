<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuBarang extends Model
{
    protected $table = 'menu_barang';

    protected $fillable = [
        'nama',
        'kategori',
        'harga_jual',
        'stok',
        'satuan',
        'stok_minimum',
        'is_dijual',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:3',
            'stok_minimum' => 'decimal:3',
            'harga_jual' => 'integer',
            'is_dijual' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(
            DetailTransaksi::class,
            'menu_barang_id'
        );
    }
}
