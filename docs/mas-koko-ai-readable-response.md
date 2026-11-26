# Mas Koko AI — Perbaikan keterbacaan jawaban

## Perubahan

1. Prompt menggunakan Bahasa Indonesia sederhana, tanpa tabel Markdown.
2. AI diarahkan menjawab dengan kesimpulan, beberapa poin pendukung, dan saran hanya bila diperlukan.
3. Definisi stok dibuat eksplisit:
   - `active_count`: jumlah barang aktif di master.
   - `for_sale_count`: jumlah barang yang ditandai untuk dijual (`is_dijual=true`), bukan jumlah unit terjual.
   - `out_count`: jumlah barang aktif dengan stok nol atau kurang.
   - `low_count`: jumlah barang aktif dengan stok di atas nol tetapi sama dengan atau di bawah batas minimum.
   - `safe_count`: jumlah barang aktif dengan stok di atas batas minimum.
4. Data stok diberi `as_of` dan AI dilarang mengaitkannya dengan rentang tanggal penjualan.
5. Markdown dari Groq dirender dengan `AIResponseFormatter`. HTML mentah di-strip dan tautan tidak aman ditolak.

## File

- `app/Services/AI/PromptBuilder.php` — dimodifikasi.
- `app/Services/AI/BusinessInsightService.php` — dimodifikasi.
- `app/Services/AI/AIResponseFormatter.php` — baru.
- `resources/views/filament/pages/mas-koko-a-i.blade.php` — dimodifikasi.
- `tests/Feature/AI/AIResponseFormatterTest.php` — baru.
- `tests/Feature/AI/BusinessInsightServiceTest.php` — dimodifikasi.

## Penerapan

Ekstrak ZIP ke root repository Laravel dan izinkan overwrite file yang sama.

## Pengujian

```bash
php artisan test --filter=AIResponseFormatterTest
php artisan test --filter=BusinessInsightServiceTest
php artisan test --filter=MasKokoAITest
```

## Catatan

Riwayat chat lama di session tetap menyimpan teks lama. Hapus percakapan di halaman Mas Koko AI untuk memulai percakapan dengan prompt baru. Jawaban baru akan menggunakan format dan definisi data yang diperbaiki.
