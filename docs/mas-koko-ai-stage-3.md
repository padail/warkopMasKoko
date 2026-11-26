# Mas Koko AI — Tahap 3: Interface & Prompt Engineering

## Prasyarat

Tahap 1 dan Tahap 2 harus sudah diterapkan:

- `app/Services/AI/GroqService.php`
- `app/Services/AI/BusinessInsightService.php`
- Konfigurasi `services.groq` di `config/services.php`

## File baru

- `app/Services/AI/PromptBuilder.php`
- `app/Filament/Pages/MasKokoAI.php`
- `resources/views/filament/pages/mas-koko-a-i.blade.php`
- `tests/Feature/AI/MasKokoAITest.php`

Tidak ada perubahan pada `AdminPanelProvider.php` karena panel sudah menggunakan `discoverPages()` untuk menemukan halaman Filament di `app/Filament/Pages`.

## URL

`/admin/mas-koko-ai`

Halaman hanya dapat diakses oleh user dengan role `admin`. Model `User` juga sudah membatasi akses panel Filament kepada admin.

## Perilaku

- Periode default adalah tujuh hari terakhir termasuk hari ini.
- Rentang tanggal maksimal 31 hari.
- Ringkasan bisnis dihitung oleh `BusinessInsightService`.
- Pertanyaan dikirim melalui `GroqService`.
- `PromptBuilder` membuat system prompt di sisi server dan menyisipkan konteks bisnis aktual.
- Riwayat percakapan disimpan sementara pada session server per user.
- Maksimal delapan pesan riwayat dikirim/dipertahankan (empat pasang pesan).
- Maksimal sepuluh pertanyaan per user per menit.
- Tombol hapus menghapus riwayat session.
- AI hanya memberi analisis; tidak dapat mengubah transaksi atau stok.

## Menjalankan test

```bash
php artisan test --filter=MasKokoAITest
```

Pastikan ekstensi PDO SQLite aktif jika test suite menggunakan SQLite in-memory.

## Catatan implementasi

Filament Page didaftarkan melalui mekanisme `discoverPages()` yang sudah ada. Tidak perlu menambahkan route manual atau migration.

Jika halaman tidak muncul di navigasi setelah file disalin, bersihkan cache:

```bash
php artisan optimize:clear
```
