<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuBarang extends Model
{
    public const STATUS_STOK_NORMAL = 'normal';

    public const STATUS_STOK_MENIPIS = 'menipis';

    public const STATUS_STOK_HABIS = 'habis';

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
        'stok_diperbarui_oleh',
        'alasan_perubahan_stok',
        'stok_diperbarui_pada',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:3',
            'stok_minimum' => 'decimal:3',
            'harga_jual' => 'integer',
            'is_dijual' => 'boolean',
            'is_active' => 'boolean',
            'stok_diperbarui_pada' => 'datetime',
        ];
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(
            DetailTransaksi::class,
            'menu_barang_id'
        );
    }

    public function stokDiperbaruiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stok_diperbarui_oleh');
    }

    public function getStatusStokAttribute(): string
    {
        if ((float) $this->stok <= 0) {
            return self::STATUS_STOK_HABIS;
        }

        if ((float) $this->stok <= (float) $this->stok_minimum) {
            return self::STATUS_STOK_MENIPIS;
        }

        return self::STATUS_STOK_NORMAL;
    }
}
