<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerahTerimaKas extends Model
{
    protected $table = 'serah_terima_kas';

    protected $fillable = [
        'user_id',
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
    ];

    protected function casts(): array
    {
        return [
            'tanggal_serah' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
