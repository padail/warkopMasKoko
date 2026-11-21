<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerahTerimaKas extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_TERVERIFIKASI = 'terverifikasi';

    public const SELISIH_SESUAI = 'sesuai';

    public const SELISIH_LEBIH = 'lebih';

    public const SELISIH_KURANG = 'kurang';

    protected $table = 'serah_terima_kas';

    protected $fillable = [
        'user_id',
        'diterima_oleh',
        'tanggal_serah',
        'uang_harus_disetor',
        'uang_aktual',
        'selisih',
        'tunai_harus_disetor',
        'tunai_aktual',
        'selisih_tunai',
        'non_tunai_harus_disetor',
        'non_tunai_aktual',
        'selisih_non_tunai',
        'status',
        'catatan',
        'diserahkan_pada',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_serah' => 'date',
            'diserahkan_pada' => 'datetime',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diterimaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function isTerverifikasi(): bool
    {
        return $this->status === self::STATUS_TERVERIFIKASI;
    }

    public function getSelisihKlasifikasiAttribute(): string
    {
        return self::klasifikasiSelisih((int) $this->selisih);
    }

    public static function klasifikasiSelisih(int $selisih): string
    {
        return match (true) {
            $selisih > 0 => self::SELISIH_LEBIH,
            $selisih < 0 => self::SELISIH_KURANG,
            default => self::SELISIH_SESUAI,
        };
    }
}
