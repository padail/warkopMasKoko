# Struktur Folder dan Batas Area Kerja Tim

## Warkop Management System - Warkop Mas Koko

Dokumen ini menetapkan struktur folder agar tiga developer dapat
mengerjakan modul masing-masing dalam satu repository Laravel 13.43.0
tanpa membuat project terpisah.

Prinsip yang digunakan: - Satu repository GitHub. - Satu aplikasi
Laravel. - Satu database dengan migration bersama. - Setiap anggota
memiliki folder modul yang menjadi tanggung jawab utamanya. - File inti
yang dipakai bersama hanya diubah melalui koordinasi dan Pull Request. -
Project Manager bertanggung jawab atas integrasi dan persetujuan
perubahan bersama.

------------------------------------------------------------------------

## 1. Struktur Folder Utama

``` text
warkop-management/
│
├── app/
│   ├── Filament/                         # RAFIF: dashboard owner
│   │   ├── Pages/
│   │   │   └── OwnerDashboard.php
│   │   ├── Widgets/
│   │   │   ├── PendapatanHariIni.php
│   │   │   ├── RekapMetodePembayaran.php
│   │   │   ├── TransaksiTerbaru.php
│   │   │   ├── PenjualanPerKaryawan.php
│   │   │   ├── StokMenipis.php
│   │   │   └── SerahTerimaMenungguVerifikasi.php
│   │   └── Resources/
│   │       └── SerahTerimaKasResource/     # jika sesuai versi Filament terpasang
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Kasir/                     # ADHITYA
│   │   │   │   ├── KasirController.php
│   │   │   │   └── TransaksiController.php
│   │   │   │
│   │   │   ├── Karyawan/                 # ADHITYA
│   │   │   │   └── LaporanHarianController.php
│   │   │   │
│   │   │   └── Stok/                     # GADING
│   │   │       └── StokController.php
│   │   │
│   │   ├── Middleware/                   # file bersama, koordinasi PM
│   │   └── Requests/
│   │       ├── Kasir/                    # ADHITYA
│   │       └── Stok/                     # GADING
│   │
│   ├── Models/                           # model inti: PM; model baru sesuai modul
│   │   ├── User.php                      # model inti, jangan diubah tanpa koordinasi
│   │   ├── MenuBarang.php                # model inti, jangan diubah tanpa koordinasi
│   │   ├── Transaksi.php                 # model inti, jangan diubah tanpa koordinasi
│   │   ├── DetailTransaksi.php           # model inti, jangan diubah tanpa koordinasi
│   │   └── SerahTerimaKas.php            # ADHITYA, jika tabel dibuat
│   │
│   ├── Services/
│   │   ├── Kasir/                        # ADHITYA
│   │   │   ├── TransaksiService.php
│   │   │   └── SerahTerimaKasService.php
│   │   │
│   │   ├── Stok/                         # GADING
│   │   │   └── StokService.php
│   │   │
│   │   └── OwnerDashboard/               # RAFIF, opsional untuk query dashboard
│   │       └── OwnerDashboardService.php
│   │
│   └── Providers/
│       └── Filament/                     # RAFIF, jika provider dibuat oleh Filament
│           └── AdminPanelProvider.php
│
├── bootstrap/
│   └── app.php                           # PM: konfigurasi inti Laravel
│
├── database/
│   ├── factories/
│   ├── migrations/                       # folder bersama, wajib review PM
│   │   ├── ...migration baseline...
│   │   └── xxxx_xx_xx_xxxxxx_create_serah_terima_kas_table.php
│   └── seeders/                          # PM: data awal dan akun role
│
├── docs/                                 # dokumentasi teknis tambahan
│   ├── PEMBAGIAN_TUGAS.md
│   └── STRUKTUR_FOLDER_TIM.md
│
├── resources/
│   ├── css/
│   │   ├── app.css                       # PM: entry point CSS bersama
│   │   └── modules/
│   │       ├── kasir.css                 # ADHITYA, jika diperlukan
│   │       └── stok.css                  # GADING, jika diperlukan
│   │
│   ├── js/
│   │   ├── app.js                        # PM: entry point JS bersama
│   │   └── modules/
│   │       ├── kasir.js                  # ADHITYA
│   │       └── stok.js                   # GADING
│   │
│   └── views/
│       ├── layouts/                      # PM: layout utama bersama
│       │   └── app.blade.php
│       │
│       ├── components/                   # komponen umum, koordinasi PM
│       │
│       ├── kasir/                        # ADHITYA
│       │   ├── index.blade.php
│       │   └── partials/
│       │       ├── daftar-barang.blade.php
│       │       ├── keranjang.blade.php
│       │       └── pembayaran.blade.php
│       │
│       ├── transaksi/                    # ADHITYA
│       │   └── detail.blade.php
│       │
│       ├── karyawan/                     # ADHITYA
│       │   └── laporan-harian/
│       │       ├── index.blade.php
│       │       └── detail-serah-terima.blade.php
│       │
│       └── stok/                         # GADING
│           ├── index.blade.php
│           ├── edit.blade.php
│           └── partials/
│               ├── tabel-stok.blade.php
│               └── form-stok.blade.php
│
├── routes/
│   ├── web.php                           # PM: memuat route modul
│   ├── kasir.php                         # ADHITYA
│   ├── karyawan.php                      # ADHITYA
│   └── stok.php                          # GADING
│
├── tests/
│   ├── Feature/
│   │   ├── Kasir/                        # ADHITYA
│   │   ├── Karyawan/                     # ADHITYA
│   │   ├── Stok/                          # GADING
│   │   └── Filament/                     # RAFIF
│   └── Unit/
│
├── .env.example                          # PM: jangan masukkan secret
├── composer.json                         # PM: dependency bersama
├── composer.lock
├── package.json                          # PM: dependency frontend bersama
├── package-lock.json
├── vite.config.js                        # PM: konfigurasi asset bersama
└── README.md
```

**Catatan:** nama model inti di atas mengikuti nama yang digunakan dalam
rancangan proyek. Sebelum membuat atau mengubah file, cocokkan dengan
nama file aktual di repository. Jika nama model yang sudah ada berbeda,
pertahankan nama yang sudah digunakan. Jangan membuat model duplikat.

Struktur Filament dapat berbeda sesuai versi Filament yang terpasang.
Gunakan hasil generator dan struktur versi aktual. Folder
`Resources/SerahTerimaKasResource` merupakan contoh lokasi konseptual,
bukan perintah untuk memaksakan struktur versi tertentu.

------------------------------------------------------------------------

## 2. Batas Area Kerja Setiap Anggota

### A. Adhitya Arif Atahillah

**Modul:** Kasir, transaksi, laporan harian karyawan, dan pengiriman
serah terima kas.

Folder utama yang menjadi tanggung jawab:

``` text
app/Http/Controllers/Kasir/
app/Http/Controllers/Karyawan/
app/Http/Requests/Kasir/
app/Models/SerahTerimaKas.php
app/Services/Kasir/
database/migrations/                 # hanya migration baru, setelah review PM
resources/views/kasir/
resources/views/transaksi/
resources/views/karyawan/
resources/js/modules/kasir.js
resources/css/modules/kasir.css
routes/kasir.php
routes/karyawan.php
tests/Feature/Kasir/
tests/Feature/Karyawan/
```

Pekerjaan: 1. Membuat tampilan kasir Tailwind. 2. Membuat pencarian dan
pemilihan barang. 3. Membuat keranjang yang dapat menambah, mengurangi,
mengganti, dan menghapus barang. 4. Membuat proses pembayaran tunai,
QRIS, dan GoPay. 5. Menyimpan transaksi dan detail transaksi. 6.
Memastikan transaksi mencatat pengguna yang login. 7. Membuat laporan
penjualan harian milik karyawan yang login. 8. Menghitung uang tunai
yang harus diserahkan. 9. Membuat pengiriman laporan serah terima. 10.
Membuat pengujian transaksi dan laporan.

**Jangan mengubah tanpa koordinasi:** - `app/Models/User.php` - model
inti transaksi dan barang - `routes/web.php` -
`resources/views/layouts/app.blade.php` - `resources/js/app.js` -
`vite.config.js` - migration baseline yang sudah ada.

### B. Rafif Ralianshah Zihni

**Modul:** Dashboard owner, laporan keseluruhan, akses admin, dan
verifikasi serah terima melalui Filament.

Folder utama:

``` text
app/Filament/
app/Providers/Filament/
app/Services/OwnerDashboard/
tests/Feature/Filament/
```

Pekerjaan: 1. Menyiapkan panel Filament untuk owner. 2. Membatasi akses
panel hanya untuk role `admin`. 3. Membuat widget pendapatan hari ini.
4. Membuat widget jumlah transaksi dan rekap metode pembayaran. 5.
Membuat widget transaksi terbaru. 6. Membuat ringkasan penjualan per
karyawan. 7. Membuat widget stok menipis. 8. Membuat daftar serah terima
yang menunggu verifikasi. 9. Membuat detail dan aksi verifikasi serah
terima. 10. Menyimpan identitas admin dan waktu verifikasi. 11. Membuat
pengujian akses panel dan verifikasi.

Rafif menggunakan model `SerahTerimaKas` dan service serah terima yang
dibuat Adhitya. Jangan membuat model atau tabel serah terima kedua.

**Jangan mengubah tanpa koordinasi:** - `app/Models/User.php` - model
inti transaksi dan barang - model `SerahTerimaKas.php` milik Adhitya -
migration baseline - `bootstrap/app.php` - `composer.json` -
`routes/web.php`.

Jika Filament memerlukan perubahan konfigurasi panel atau provider yang
berdampak ke aplikasi, ajukan melalui Pull Request dan koordinasikan
dengan Project Manager.

### C. Gading Rangga Pamungkas

**Modul:** Manajemen stok Tailwind dan pembaruan stok oleh karyawan.

Folder utama:

``` text
app/Http/Controllers/Stok/
app/Http/Requests/Stok/
app/Services/Stok/
resources/views/stok/
resources/js/modules/stok.js
resources/css/modules/stok.css
routes/stok.php
tests/Feature/Stok/
```

Pekerjaan: 1. Membuat halaman daftar stok. 2. Membuat pencarian barang.
3. Membuat filter kategori dan status stok. 4. Membuat pagination. 5.
Membuat form pembaruan jumlah stok. 6. Mengizinkan admin dan karyawan
memperbarui jumlah stok. 7. Membatasi karyawan agar tidak mengubah
harga, nama, kategori, atau status barang. 8. Membuat indikator stok
normal, menipis, dan habis. 9. Memastikan stok tidak pernah bernilai
negatif. 10. Menguji pembaruan stok bersamaan dengan proses transaksi.

Gading harus berkoordinasi dengan Adhitya mengenai proses pengurangan
stok ketika transaksi selesai. Hanya boleh ada satu aturan pengurangan
stok yang disepakati. Jangan membuat pengurangan stok kedua di
controller stok.

**Jangan mengubah tanpa koordinasi:** - `app/Models/User.php` - model
inti barang - service transaksi milik Adhitya - migration baseline -
`routes/web.php` - `resources/views/layouts/app.blade.php` -
`resources/js/app.js` - `vite.config.js`.

------------------------------------------------------------------------

## 3. File Bersama yang Dikendalikan Project Manager

File berikut digunakan oleh lebih dari satu modul. Untuk mencegah
konflik, Project Manager menjadi penanggung jawab utama.

  ------------------------------------------------------------------------
  File/folder                  Penanggung jawab      Aturan
  ---------------------------- --------------------- ---------------------
  `routes/web.php`             Project Manager       Mendaftarkan file
                                                     route modul

  `bootstrap/app.php`          Project Manager       Konfigurasi inti
                                                     Laravel

  `app/Models/User.php`        Project Manager       Perubahan role/relasi
                                                     melalui koordinasi

  Model inti barang dan        Project Manager       Perubahan relasi
  transaksi                                          melalui PR

  Migration baseline           Project Manager       Jangan diedit setelah
                                                     digunakan bersama

  `database/seeders/`          Project Manager       Menjaga akun dan data
                                                     awal konsisten

  `resources/views/layouts/`   Project Manager       Layout bersama

  `resources/js/app.js`        Project Manager       Mengimpor JS modul

  `resources/css/app.css`      Project Manager       Mengimpor CSS modul

  `vite.config.js`             Project Manager       Entry point asset

  `composer.json`              Project Manager       Dependency PHP

  `package.json`               Project Manager       Dependency frontend
  ------------------------------------------------------------------------

Anggota boleh mengusulkan perubahan pada file bersama. Buat Pull Request
terpisah atau beri catatan jelas pada Pull Request fitur. Jangan
melakukan perubahan diam-diam pada file yang sedang dikerjakan anggota
lain.

------------------------------------------------------------------------

## 4. Pembagian Route

Gunakan file route terpisah agar anggota tidak perlu mengedit
`routes/web.php` setiap kali menambahkan endpoint.

### `routes/kasir.php` - Adhitya

Contoh tanggung jawab route: - `GET /kasir` - `POST /kasir/transaksi` -
`GET /transaksi/{transaksi}`

### `routes/karyawan.php` - Adhitya

Contoh tanggung jawab route: - `GET /karyawan/laporan-harian` -
`GET /karyawan/laporan-harian/{tanggal}` - `POST /karyawan/serah-terima`

### `routes/stok.php` - Gading

Contoh tanggung jawab route: - `GET /stok` -
`GET /stok/{menuBarang}/edit` - `PATCH /stok/{menuBarang}`

Route Filament dikelola oleh panel provider Filament. Rafif tidak perlu
membuat file `routes/filament.php` kecuali arsitektur aktual project
memang memerlukannya.

### Integrasi route oleh Project Manager

Pada `routes/web.php`, Project Manager mendaftarkan file route modul.
Contoh:

``` php
<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/kasir.php';
require __DIR__.'/karyawan.php';
require __DIR__.'/stok.php';
```

File yang di-`require` tersebut dimuat sebagai bagian dari file route
web. Tetap gunakan middleware `auth` dan middleware role yang sesuai
pada route group masing-masing.

Contoh pola pada file modul:

``` php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Stok\StokController;

Route::middleware(['auth'])
    ->prefix('stok')
    ->name('stok.')
    ->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('index');
        Route::patch('/{menuBarang}', [StokController::class, 'update'])->name('update');
    });
```

Nama middleware role harus mengikuti alias yang benar-benar sudah
didaftarkan di project. Jangan mengasumsikan alias `role` tersedia
sebelum memeriksa konfigurasi.

------------------------------------------------------------------------

## 5. Aturan Migration dan Model

Migration Laravel tetap disimpan di satu folder:

``` text
database/migrations/
```

Jangan membuat subfolder migration per anggota karena konfigurasi
migration Laravel standar membaca direktori migration yang ditentukan
framework. Pemisahan tanggung jawab dilakukan melalui nama file, Issue,
dan Pull Request.

Aturan: 1. Migration baseline yang sudah di-push tidak boleh diedit. 2.
Jika Adhitya membutuhkan tabel serah terima, buat migration baru. 3.
Jika Gading membutuhkan tabel riwayat stok, ajukan rancangan terlebih
dahulu. 4. Project Manager memeriksa foreign key, tipe data, index,
unique constraint, dan dampak migration. 5. Setiap migration harus diuji
pada database lokal. 6. Jangan menjalankan `migrate:fresh` pada database
bersama. 7. Jangan membuat tabel atau model duplikat.

Model inti tetap berada di `app/Models`. Model baru boleh ditambahkan
oleh anggota sesuai modulnya, tetapi relasi ke model inti harus
dikoordinasikan.

------------------------------------------------------------------------

## 6. Integrasi JavaScript dan CSS

Agar file frontend tidak bertabrakan:

-   Adhitya menulis interaksi kasir di `resources/js/modules/kasir.js`.
-   Gading menulis interaksi stok di `resources/js/modules/stok.js`.
-   Adhitya menulis CSS khusus kasir di
    `resources/css/modules/kasir.css` jika benar-benar diperlukan.
-   Gading menulis CSS khusus stok di `resources/css/modules/stok.css`
    jika benar-benar diperlukan.
-   Project Manager mengatur import modul melalui `resources/js/app.js`
    dan `resources/css/app.css`.

Jangan mengubah entry point bersama secara bersamaan. Sampaikan file
modul yang perlu diimpor melalui Pull Request.

Gunakan utility Tailwind langsung di Blade jika sudah cukup. Hindari CSS
khusus yang tidak diperlukan.

------------------------------------------------------------------------

## 7. Aturan Namespace dan Penamaan

Gunakan namespace sesuai folder:

``` php
namespace App\Http\Controllers\Kasir;
namespace App\Http\Controllers\Karyawan;
namespace App\Http\Controllers\Stok;
namespace App\Services\Kasir;
namespace App\Services\Stok;
```

Nama class menggunakan PascalCase: - `KasirController` -
`TransaksiController` - `LaporanHarianController` - `StokController` -
`TransaksiService` - `StokService`

Nama view menggunakan dot notation: - `kasir.index` -
`transaksi.detail` - `karyawan.laporan-harian.index` - `stok.index` -
`stok.edit`

Nama route: - `kasir.index` - `kasir.transaksi.store` -
`karyawan.laporan.index` - `karyawan.serah-terima.store` -
`stok.index` - `stok.update`

Nama tabel, kolom, status, dan metode pembayaran harus mengikuti
migration serta kontrak pada README. Jangan membuat variasi sendiri.

------------------------------------------------------------------------

## 8. Alur Kerja GitHub

Setiap anggota bekerja pada branch fitur masing-masing.

Contoh:

``` text
main
└── develop
    ├── feature/adhitya-kasir
    ├── feature/adhitya-laporan-karyawan
    ├── feature/rafif-filament-dashboard
    ├── feature/rafif-verifikasi-kas
    └── feature/gading-manajemen-stok
```

Perintah awal:

``` bash
git switch develop
git pull origin develop
git switch -c feature/nama-fitur
```

Setelah selesai:

``` bash
git add .
git commit -m "feat: deskripsi fitur"
git push -u origin feature/nama-fitur
```

Buat Pull Request menuju `develop`. Jangan langsung merge jika perubahan
menyentuh migration, model inti, konfigurasi route, atau hak akses
sebelum Project Manager melakukan review.

Satu anggota boleh memiliki beberapa feature branch. Jangan menumpuk
semua pekerjaan dalam satu branch besar jika dapat dipisah menjadi
beberapa Pull Request yang lebih mudah ditinjau.

------------------------------------------------------------------------

## 9. Urutan Integrasi yang Disarankan

1.  Semua anggota memastikan baseline Laravel dapat berjalan.
2.  Project Manager memastikan branch `develop` tersedia.
3.  Adhitya mengajukan rancangan tabel dan model serah terima.
4.  Project Manager meninjau dan menyetujui struktur database.
5.  Adhitya mengembangkan transaksi dan laporan karyawan.
6.  Gading mengembangkan halaman stok dan menyepakati kontrak
    pengurangan stok dengan Adhitya.
7.  Rafif mengembangkan Filament menggunakan model dan struktur data
    yang telah disepakati.
8.  Setiap anggota mengirim Pull Request secara bertahap.
9.  Project Manager mengintegrasikan route, asset, dan file bersama.
10. Tim menjalankan pengujian lintas modul.

------------------------------------------------------------------------

## 10. Checklist Sebelum Anggota Mulai

### Adhitya

-   [ ] Sudah membaca README dan dokumen pembagian tugas.
-   [ ] Sudah memeriksa model dan migration transaksi.
-   [ ] Sudah mengetahui struktur harga dan stok barang.
-   [ ] Sudah mengajukan rancangan migration serah terima.
-   [ ] Sudah membuat branch fitur.
-   [ ] Tidak mengubah file inti tanpa koordinasi.

### Rafif

-   [ ] Sudah memeriksa versi Filament yang terpasang.
-   [ ] Sudah memastikan panel provider yang digunakan.
-   [ ] Sudah memahami nilai role `admin` dan `karyawan`.
-   [ ] Sudah menyepakati struktur data serah terima dengan Adhitya.
-   [ ] Sudah membuat branch fitur.
-   [ ] Tidak membuat model atau tabel serah terima duplikat.

### Gading

-   [ ] Sudah memeriksa struktur tabel barang.
-   [ ] Sudah memahami kolom stok dan stok minimum.
-   [ ] Sudah menyepakati proses pengurangan stok dengan Adhitya.
-   [ ] Sudah membuat branch fitur.
-   [ ] Tidak mengubah harga atau identitas barang melalui akses
    karyawan.

### Project Manager

-   [ ] Branch `develop` tersedia.
-   [ ] File route modul akan didaftarkan dari `routes/web.php`.
-   [ ] File inti dan migration baseline telah ditetapkan sebagai area
    bersama.
-   [ ] Aturan review Pull Request sudah disepakati.
-   [ ] Setiap anggota memiliki Issue dan branch masing-masing.

------------------------------------------------------------------------

## 11. Keputusan Penting

Struktur ini adalah pembagian area kerja, bukan pemisahan aplikasi.
Semua modul tetap memakai autentikasi, model inti, database, layout, dan
aturan bisnis yang sama.

Jika anggota membutuhkan perubahan pada file bersama, mereka harus: 1.
Menjelaskan kebutuhan perubahan. 2. Membuat Issue atau komentar pada
Issue terkait. 3. Mengajukan perubahan melalui Pull Request. 4. Menunggu
review Project Manager. 5. Menguji ulang modul yang terdampak setelah
merge.

Dengan aturan ini, setiap anggota dapat bekerja secara paralel tanpa
membuat project terpisah atau saling menimpa pekerjaan.
