<?php

namespace App\Models;

use Database\Factories\PengeluaranFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    public const METODE_TUNAI = 'tunai';

    public const METODE_NON_TUNAI = 'non_tunai';

    protected $table = 'pengeluaran';

    protected $fillable = [
        'user_id',
        'tanggal_pengeluaran',
        'kategori',
        'keterangan',
        'jumlah',
        'metode_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengeluaran' => 'date',
            'jumlah' => 'integer',
        ];
    }

    /** @use HasFactory<PengeluaranFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
