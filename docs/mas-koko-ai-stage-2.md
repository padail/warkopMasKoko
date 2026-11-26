# Mas Koko AI — Tahap 2: Business Intelligence Service

## Tujuan

`BusinessInsightService` mengambil data dari database dan menyusun konteks terstruktur untuk diberikan kepada Groq pada tahap integrasi berikutnya.

Service ini tidak memanggil API Groq dan tidak mengubah data bisnis.

## File

- `app/Services/AI/BusinessInsightService.php`
- `tests/Feature/AI/BusinessInsightServiceTest.php`

## Pemakaian

```php
use App\Services\AI\BusinessInsightService;
use App\Services\OwnerDashboard\OwnerDashboardService;

$context = app(BusinessInsightService::class)->buildContext([
    'periode' => OwnerDashboardService::PRESET_HARI_INI,
]);
```

Untuk rentang tanggal:

```php
$context = app(BusinessInsightService::class)->buildContext([
    'periode' => OwnerDashboardService::PRESET_RENTANG,
    'start' => '2026-11-01',
    'end' => '2026-11-26',
]);
```

## Isi konteks

- `period`: periode analisis dan jumlah hari.
- `sales`: omzet, jumlah transaksi, rata-rata transaksi, dan rincian metode pembayaran.
- `sales_comparison`: perbandingan dengan periode sebelumnya yang durasinya sama.
- `top_products`: maksimal lima produk terlaris berdasarkan kuantitas.
- `stock`: ringkasan stok dan produk yang berada di bawah atau sama dengan stok minimum.
- `expenses`: jumlah pengeluaran dan agregasi berdasarkan kategori.
- `data_limitations`: batas interpretasi untuk mencegah kesimpulan AI yang tidak didukung data.

## Aturan perhitungan

1. Penjualan hanya menggunakan transaksi berstatus `selesai`.
2. Perbandingan menggunakan periode sebelumnya dengan durasi yang sama.
3. Persentase perubahan tidak dihitung jika omzet periode sebelumnya nol; nilainya `null`.
4. Produk terlaris diurutkan berdasarkan jumlah unit, kemudian omzet.
5. Snapshot nama produk dari detail transaksi digunakan agar perubahan nama master tidak mengubah histori penjualan.
6. Stok adalah kondisi saat ini, bukan stok historis pada akhir periode.
7. Pengeluaran menggunakan tanggal `tanggal_pengeluaran`.
8. Selisih omzet dan pengeluaran tidak boleh disebut laba bersih karena data harga pokok penjualan dan seluruh biaya usaha belum tentu tersedia.

## Pengujian

```bash
php artisan test --filter=BusinessInsightServiceTest
```

Pengujian menggunakan database testing dan tidak membutuhkan koneksi Groq.
