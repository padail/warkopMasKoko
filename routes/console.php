<?php

use App\Services\AI\GroqService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('groq:check', function (GroqService $groq) {
    try {
        $model = $groq->checkConnection();

        $this->info('Koneksi Groq berhasil.');
        $this->line('Model aktif: '.$model['id']);

        return 0;
    } catch (\Throwable $exception) {
        report($exception);
        $this->error('Koneksi Groq gagal. Periksa GROQ_API_KEY, GROQ_MODEL, dan log aplikasi.');

        return 1;
    }
})->purpose('Memeriksa koneksi API Groq dan model yang dikonfigurasi');
