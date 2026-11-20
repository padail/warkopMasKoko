# Warkop Management System

Aplikasi operasional dan kasir untuk Warkop Mas Koko. Proyek menggunakan
Laravel Framework 13.43.0. Aplikasi memiliki dua antarmuka: halaman
operasional berbasis Laravel Blade dan Tailwind CSS, serta dashboard
owner berbasis Filament.

Dokumen pembagian kerja terperinci tersedia di
[`PEMBAGIAN_TUGAS.md`](PEMBAGIAN_TUGAS.md).

## 1. Informasi Proyek

  Informasi                Keterangan
  ------------------------ ---------------------------------------------
  Nama aplikasi            Warkop Management System
  Mitra                    Warkop Mas Koko
  Framework                Laravel 13.43.0
  UI operasional           Blade, Tailwind CSS, JavaScript
  Dashboard owner          Filament
  Database                 Mengikuti konfigurasi repository dan `.env`
  Zona waktu operasional   Asia/Jakarta
  Repository               Tambahkan URL GitHub kelompok

**Status:** project Laravel, database awal, dan repository sudah
disiapkan oleh Project Manager. Anggota melanjutkan pengembangan dari
baseline tersebut. Jangan membuat ulang project atau mengganti struktur
database tanpa koordinasi.

## 2. Tujuan Aplikasi

1.  Mempercepat pencatatan pesanan pelanggan.
2.  Memungkinkan kasir menambah, mengurangi, mengganti, dan menghapus
    barang sebelum transaksi disimpan.
3.  Menghitung total pembayaran dan kembalian secara otomatis.
4.  Menyimpan transaksi dan detail barang secara konsisten.
5.  Memungkinkan karyawan memperbarui jumlah stok melalui halaman
    Tailwind.
6.  Menyediakan laporan penjualan harian untuk setiap karyawan.
7.  Mencatat uang tunai yang diserahkan karyawan kepada owner dan
    menghitung selisihnya.
8.  Menyediakan dashboard owner untuk memantau penjualan seluruh
    karyawan.
9.  Menyediakan peringatan stok minimum.
10. Mencatat pemasukan dan pengeluaran karyawan menurut metode tunai dan
     non-tunai.

## 3. Role dan Hak Akses

Aplikasi menggunakan dua role:

-   `admin`: pemilik atau owner Warkop Mas Koko.
-   `karyawan`: pekerja atau kasir.

### Admin

Admin menggunakan Filament untuk dashboard owner dan Tailwind untuk
halaman operasional.

Hak akses: - Mengakses dashboard Filament. - Melihat penjualan seluruh
karyawan. - Melihat rekap metode pembayaran. - Melihat dan memverifikasi
serah terima kas. - Mengakses halaman kasir. - Mengakses halaman
manajemen stok. - Memperbarui stok. - Mengelola nama, kategori, harga,
satuan, dan status barang. - Mengelola akun karyawan jika fitur tersebut
diaktifkan.

### Karyawan

Karyawan menggunakan halaman Tailwind. Karyawan tidak boleh mengakses
panel Filament.

Hak akses: - Mengakses halaman kasir. - Mengelola keranjang pesanan. -
Memproses pembayaran tunai, QRIS, dan GoPay. - Melihat dan memperbarui
jumlah stok. - Melihat laporan penjualan miliknya sendiri. - Menginput
uang aktual yang diserahkan kepada owner. - Melihat status verifikasi
serah terima miliknya.

Karyawan tidak boleh mengubah harga atau identitas barang, mengubah
role, melihat laporan karyawan lain, memverifikasi serah terima, atau
mengakses Filament.

### Matriks Akses

  Fitur                              Admin     Karyawan
  -------------------------------- ---------- ----------
  Dashboard Filament                   Ya       Tidak
  Halaman kasir Tailwind               Ya         Ya
  Membuat transaksi                    Ya         Ya
  Melihat stok                         Ya         Ya
  Memperbarui jumlah stok              Ya         Ya
  Mengubah nama, kategori, harga       Ya       Tidak
  Melihat laporan semua karyawan       Ya       Tidak
  Melihat laporan sendiri              Ya         Ya
  Mengirim serah terima kas         Opsional      Ya
  Memverifikasi serah terima           Ya       Tidak

Semua pembatasan harus diterapkan di server melalui middleware, policy,
atau otorisasi Filament. Menyembunyikan tombol saja tidak cukup.

## 4. Pembagian Teknologi

  Bagian                    Teknologi
  ------------------------- --------------------------------------------
  Backend                   Laravel 13
  ORM                       Eloquent
  Kasir                     Blade + Tailwind CSS + JavaScript
  Manajemen stok            Blade + Tailwind CSS
  Laporan harian karyawan   Blade + Tailwind CSS
  Dashboard owner           Filament
  Asset frontend            Vite dan dependency yang sudah ada
  Pengujian                 PHPUnit/Pest sesuai konfigurasi repository

**Ketentuan:** halaman kasir dan manajemen stok menggunakan Tailwind.
Dashboard owner menggunakan Filament. Jangan membuat halaman kasir atau
manajemen stok sebagai Filament Resource tanpa persetujuan Project
Manager.

## 5. Alur Bisnis dan Aturan Teknis

### Kasir

1.  Pengguna login.
2.  Sistem memeriksa role.
3.  Admin atau karyawan membuka halaman kasir.
4.  Sistem menampilkan barang aktif yang dapat dijual.
5.  Pengguna mencari barang dan menambahkannya ke keranjang.
6.  Pengguna dapat menambah, mengurangi, mengganti, atau menghapus
    barang.
7.  Sistem menghitung subtotal dan total.
8.  Pengguna memilih metode pembayaran.
9.  Sistem memvalidasi pembayaran.
10. Sistem menyimpan transaksi dan detail transaksi dalam satu database
    transaction.
11. Sistem memperbarui stok sesuai aturan inventaris.
12. Sistem menampilkan ringkasan transaksi.

Aturan: - Browser hanya mengirim ID barang dan jumlah. Server mengambil
harga dari database. - Server menghitung ulang subtotal dan total.
Jangan mempercayai total dari JavaScript. - Barang tidak aktif atau
tidak dijual tidak boleh masuk transaksi baru. - Jumlah harus lebih
besar dari nol. - Stok tidak boleh negatif. - Simpan transaksi dan
detail menggunakan `DB::transaction()`. - Gunakan penguncian data yang
sesuai ketika membaca dan mengurangi stok. - Simpan nama dan harga saat
transaksi sebagai snapshot. - Transaksi batal tidak dihitung sebagai
penjualan selesai. - Cegah submit ganda.

### Pembayaran

Nilai metode pembayaran yang digunakan:

``` text
tunai
qris
gopay
```

Pembayaran QRIS dan GoPay diverifikasi manual oleh karyawan melalui
perangkat pembayaran. Integrasi payment gateway tidak termasuk ruang
lingkup versi pertama.

Untuk pembayaran tunai: - `total` adalah jumlah yang harus dibayar. -
`nominal_diterima` adalah uang yang diberikan pelanggan. -
`kembalian = nominal_diterima - total`. - `nominal_diterima` harus lebih
besar atau sama dengan `total`.

Untuk QRIS dan GoPay: - Karyawan memastikan pembayaran telah diterima
sebelum menyelesaikan transaksi. - Kembalian bernilai nol. - Transaksi
non-tunai tidak masuk perhitungan uang fisik yang diserahkan kepada
owner.

### Laporan Keuangan Karyawan

Laporan menampilkan catatan milik karyawan yang sedang login. Secara
default laporan mencakup semua tanggal; filter tanggal opsional untuk
melihat satu hari. Pemasukan berasal dari transaksi selesai, sementara
karyawan dapat mencatat pengeluaran dengan tanggal, kategori,
keterangan, nominal, dan metode pembayaran (`tunai` atau `non_tunai`).

Ringkasan memisahkan pemasukan dan pengeluaran tunai/non-tunai serta
menampilkan saldo bersih setiap jenis dan saldo keseluruhan. QRIS dan
GoPay dikelompokkan sebagai pemasukan non-tunai.

### Rekonsiliasi Setoran

Rumus yang digunakan:

```text
pemasukan_tunai = SUM(total transaksi tunai berstatus selesai)
pemasukan_non_tunai = SUM(total transaksi QRIS dan GoPay berstatus selesai)
saldo_tunai = pemasukan_tunai - pengeluaran_tunai
saldo_non_tunai = pemasukan_non_tunai - pengeluaran_non_tunai
setoran_tunai_diharapkan = max(0, saldo_tunai)
setoran_non_tunai_diharapkan = max(0, saldo_non_tunai)
```

Karyawan mengirim jumlah aktual setoran tunai dan non-tunai secara
terpisah untuk tanggal yang dipilih. Selisih dihitung per metode.
Transaksi batal tidak dihitung, dan `nominal_diterima` tidak dijumlahkan
karena nilainya dapat mencakup uang kembalian.

Data laporan dibatasi ke `user_id` karyawan yang sedang login. Filter
URL tidak boleh memungkinkan karyawan mengakses transaksi atau
pengeluaran karyawan lain.

### Manajemen Stok

Halaman stok menggunakan Blade dan Tailwind. Admin dan karyawan dapat: -
Melihat stok. - Mencari barang. - Memfilter kategori. - Melihat satuan
dan stok minimum. - Memperbarui jumlah stok. - Mengisi alasan pembaruan.

Karyawan hanya boleh mengubah jumlah stok. Karyawan tidak boleh mengubah
nama, kategori, harga, satuan, atau status barang. Admin memiliki hak
pengelolaan lebih luas.

Aturan: - Stok tidak boleh negatif. - Semua input divalidasi di
server. - Identitas pengubah diambil dari pengguna yang login. - Status
menipis jika `stok <= stok_minimum`. - Status habis jika `stok = 0`. -
Pembaruan stok manual tidak boleh menimpa pengurangan stok dari
transaksi secara tidak konsisten.

Jika struktur awal belum mencatat aktor pembaruan stok, ajukan migration
tambahan untuk menyimpan informasi tersebut. Riwayat stok lengkap dapat
menjadi pengembangan lanjutan setelah disetujui Project Manager.

### Dashboard Owner Filament

Filament hanya untuk admin. Widget minimum: 1. Pendapatan hari ini. 2.
Jumlah transaksi selesai. 3. Total tunai. 4. Total QRIS. 5. Total GoPay.
6. Daftar stok menipis. 7. Transaksi terbaru. 8. Penjualan per karyawan.
9. Serah terima menunggu verifikasi. 10. Selisih serah terima.

Data harus berasal dari database dan mendukung filter tanggal.

## 6. Database dan Relasi

Database awal telah dibuat dan di-push oleh Project Manager. Migration
yang ada di repository menjadi sumber acuan. Periksa migration dan model
sebelum menambah struktur. Jangan membuat ulang tabel atau mengedit
migration yang sudah dijalankan bersama.

Entitas inti: - `users`: akun admin dan karyawan. - `menu_barang`:
barang, kategori, harga, stok, satuan, status. - `transaksi`: transaksi
utama dan pengguna yang memprosesnya. - `detail_transaksi`: rincian
barang dalam transaksi.

Relasi: - `users` hasMany `transaksi`. - `transaksi` belongsTo
`users`. - `transaksi` hasMany `detail_transaksi`. - `detail_transaksi`
belongsTo `transaksi`. - `menu_barang` hasMany `detail_transaksi`. -
`detail_transaksi` belongsTo `menu_barang`.

Tabel pendukung `serah_terima_kas` ditambahkan melalui migration baru
jika belum tersedia. Jika kolom `user_id` pada transaksi belum ada,
ajukan migration tambahan. Jangan mengubah migration baseline yang sudah
dibagikan.

## 7. Standar Teknis

1.  Gunakan Laravel 13.43.0 sesuai baseline.
2.  Jangan memperbarui Laravel atau dependency utama tanpa persetujuan.
3.  Gunakan nama tabel dan kolom dari migration aktual.
4.  Gunakan Eloquent dan eager loading.
5.  Validasi input di server.
6.  Jangan mempercayai harga, total, role, user ID, atau selisih dari
    browser.
7.  Gunakan `auth()->id()` untuk menentukan pemilik transaksi.
8.  Scope laporan karyawan berdasarkan pengguna yang login.
9.  Gunakan `DB::transaction()` untuk penyimpanan transaksi dan detail.
10. Jangan menghapus barang yang sudah memiliki riwayat; nonaktifkan
    barang.
11. Jangan mengunggah `.env`, password, token, atau API key.
12. Jangan mengunggah `vendor` dan `node_modules`.
13. Pertahankan `composer.lock` dan lockfile frontend.
14. Jangan mengubah file anggota lain tanpa koordinasi.

## 8. Menjalankan Project

Persyaratan PHP harus mengikuti `composer.json`. Gunakan Composer,
Node.js/npm, Git, dan database yang dikonfigurasi pada `.env`.

Clone:

``` bash
git clone <URL_REPOSITORY_GITHUB>
cd <NAMA_FOLDER_PROJECT>
```

Install dependency:

``` bash
composer install
npm install
```

Buat environment:

``` bash
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell:

``` powershell
Copy-Item .env.example .env
```

Atur koneksi database pada `.env`, lalu:

``` bash
php artisan migrate:status
php artisan migrate
php artisan db:seed
```

Gunakan `php artisan migrate:fresh --seed` hanya pada database lokal
yang aman dihapus. Jangan jalankan pada database bersama atau database
berisi data penting.

Jalankan aplikasi:

``` bash
php artisan serve
npm run dev
```

Build frontend:

``` bash
npm run build
```

Jangan menjalankan `composer update` tanpa alasan dan persetujuan.

## 9. GitHub Workflow

Branch: - `main`: versi stabil. - `develop`: integrasi. - `feature/*`:
pekerjaan per fitur.

Contoh branch:

``` text
feature/kasir-transaksi
feature/laporan-harian-karyawan
feature/serah-terima-kas
feature/filament-owner-dashboard
feature/owner-cash-verification
feature/manajemen-stok
feature/stock-adjustment
```

Alur:

``` bash
git switch develop
git pull origin develop
git switch -c feature/nama-fitur

# Coding dan pengujian

git add .
git commit -m "feat: deskripsi fitur"
git push -u origin feature/nama-fitur
```

Buat Pull Request menuju `develop`. Jangan coding langsung di `main`
atau `develop`.

Contoh commit:

``` text
feat(kasir): implement cart management
feat(transaction): save transaction details
feat(report): add employee daily sales summary
feat(cash): add cash handover submission
feat(filament): add owner sales widgets
feat(filament): add cash handover verification
feat(inventory): add stock adjustment form
fix(auth): prevent employee access to Filament
test(transaction): cover cash payment calculation
docs: update project setup instructions
```

Pull Request harus menjelaskan tujuan, fitur, file yang diubah,
migration, cara pengujian, hasil pengujian, dan screenshot untuk
perubahan UI.

## 10. Pengujian dan Definition of Done

Kasir: - Barang aktif muncul. - Barang nonaktif tidak dapat diproses. -
Keranjang dapat ditambah, dikurangi, diganti, dan dihapus. - Total
dihitung backend. - Tunai menghitung kembalian. - QRIS/GoPay tidak
menghasilkan kembalian. - Transaksi dan detail tersimpan. - Transaksi
gagal tidak mengurangi stok. - Submit ganda tidak membuat transaksi
ganda.

Laporan karyawan: - Karyawan hanya melihat transaksi miliknya. - Filter
tanggal bekerja. - Total tunai hanya menghitung transaksi tunai
selesai. - QRIS/GoPay dan transaksi batal dikecualikan. - Selisih
dihitung server. - Laporan yang dikirim tidak dapat diubah karyawan.

Owner: - Admin dapat membuka Filament. - Karyawan ditolak. - Widget
sesuai database. - Filter tanggal bekerja. - Owner melihat seluruh
karyawan. - Owner dapat memverifikasi serah terima. - Identitas dan
waktu verifikasi tersimpan.

Stok: - Admin dan karyawan dapat membuka halaman. - Karyawan dapat
mengubah jumlah stok. - Karyawan tidak dapat mengubah harga atau
identitas barang. - Stok negatif ditolak. - Peringatan stok minimum
benar. - Pembaruan stok tidak merusak transaksi.

Perintah pemeriksaan:

``` bash
php artisan test
vendor/bin/pint
npm run build
```

Sebuah fitur selesai jika fungsi berjalan, validasi dan otorisasi
tersedia, pengujian berhasil, perubahan sudah di-commit, Pull Request
direview, dan kode digabungkan ke `develop`.

## 11. Materi Praktikum yang Diterapkan

  -----------------------------------------------------------------------
  Materi                              Penerapan
  ----------------------------------- -----------------------------------
  Routing Laravel                     Route kasir, stok, laporan, panel

  Controller                          Proses transaksi dan stok

  Blade                               Halaman operasional

  Tailwind CSS                        Kasir dan manajemen stok

  JavaScript                          Interaksi keranjang

  Migration                           Struktur dan perubahan database

  Eloquent                            Model dan relasi

  Eager loading                       Data transaksi beserta relasi

  Seeder dan Factory                  Data awal dan pengujian

  Pagination dan filter               Barang dan riwayat

  Filament                            Dashboard owner dan verifikasi

  Service class/HTTP Client           Integrasi layanan eksternal atau AI
                                      jika disetujui

  Automated testing                   Pengujian transaksi, stok, dan
                                      otorisasi
  -----------------------------------------------------------------------

Fitur AI rekomendasi kategori barang merupakan fitur tambahan. Kerjakan
setelah kasir, stok, laporan harian, dan rekonsiliasi kas stabil.

## 12. Koordinasi

Gunakan GitHub Issues untuk pekerjaan, Pull Request untuk perubahan
kode, dan diskusi tim untuk keputusan yang memengaruhi database atau
aturan bisnis.

Baca [`PEMBAGIAN_TUGAS.md`](PEMBAGIAN_TUGAS.md) untuk instruksi teknis
per anggota, daftar file, dependensi, urutan pengerjaan, dan kriteria
penerimaan.

**Catatan kontributor:** baca kedua dokumen, periksa migration yang
sudah ada, jalankan baseline di komputer sendiri, lalu mulai coding pada
feature branch. Jangan membuat ulang project Laravel atau migration yang
sudah tersedia.
