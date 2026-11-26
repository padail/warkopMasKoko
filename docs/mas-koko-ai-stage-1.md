# Mas Koko AI — Tahap 1: Fondasi Groq

## Komponen yang ditambahkan

- `app/Services/AI/GroqService.php`: pemeriksaan model dan Chat Completions.
- `config/services.php`: konfigurasi Groq.
- `.env.example`: variabel environment Groq.
- `routes/console.php`: perintah diagnostik `groq:check`.
- `tests/Feature/AI/GroqServiceTest.php`: pengujian HTTP fake.

## Konfigurasi lokal

Tambahkan ke file `.env` lokal:

```env
GROQ_API_KEY=isi_api_key_dari_groq_console
GROQ_BASE_URL=https://api.groq.com/openai/v1
GROQ_MODEL=openai/gpt-oss-120b
GROQ_TIMEOUT=30
```

Jangan commit `.env` atau membagikan API key. Pastikan `.env` tercantum dalam `.gitignore`.

## Menjalankan pemeriksaan

```bash
php artisan config:clear
php artisan groq:check
```

Koneksi berhasil akan menampilkan nama model yang terverifikasi.

## Menjalankan test

```bash
php artisan test --filter=GroqServiceTest
```

Test menggunakan `Http::fake()`, sehingga tidak memerlukan API key asli dan tidak mengirim request ke internet.

## Catatan

`openai/gpt-oss-120b` digunakan sebagai pengganti model `llama-3.3-70b-versatile` yang telah dihentikan untuk Developer Tier pada 16 Agustus 2026. Jika status model berubah di masa mendatang, ubah `GROQ_MODEL` tanpa mengubah kode service.
