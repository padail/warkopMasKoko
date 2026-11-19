<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'kode_transaksi',
        'tanggal_transaksi',
        'metode_pembayaran',
        'total',
        'nominal_diterima',
        'kembalian',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(
            DetailTransaksi::class,
            'transaksi_id'
        );
    }
}
