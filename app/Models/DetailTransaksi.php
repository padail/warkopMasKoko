<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_id',
        'menu_barang_id',
        'nama_item_snapshot',
        'harga_satuan',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(
            Transaksi::class,
            'transaksi_id'
        );
    }

    public function menuBarang(): BelongsTo
    {
        return $this->belongsTo(
            MenuBarang::class,
            'menu_barang_id'
        );
    }
}

