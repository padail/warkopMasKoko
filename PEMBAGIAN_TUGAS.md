# PEMBAGIAN TUGAS TIM

## Warkop Management System - Warkop Mas Koko

Dokumen ini menjadi acuan kerja tiga developer selain Project Manager.
Setiap anggota bertanggung jawab atas implementasi, pengujian, commit,
dan Pull Request untuk modulnya.

**Framework:** Laravel 13.43.0\
**UI operasional:** Blade + Tailwind CSS\
**Dashboard owner:** Filament\
**Role:** `admin` dan `karyawan`

------------------------------------------------------------------------

# 1. Struktur Tim

  -----------------------------------------------------------------------
  Anggota                 Posisi                  Modul utama
  ----------------------- ----------------------- -----------------------
  Adhitya Arif Atahillah  POS & Employee Sales    Kasir, transaksi,
                          Developer               laporan harian
                                                  karyawan, serah terima
                                                  kas

  Rafif Ralianshah Zihni  Filament & Owner        Dashboard owner,
                          Dashboard Developer     laporan seluruh
                                                  karyawan, verifikasi
                                                  kas, akses admin

  Gading Rangga Pamungkas Inventory & Stock       Manajemen stok
                          Developer               Tailwind, pembaruan
                                                  stok, peringatan stok
                                                  minimum, pengujian
                                                  inventaris
  -----------------------------------------------------------------------

## Tanggung jawab Project Manager

Project Manager sudah menyiapkan project Laravel, database awal, dan
repository GitHub. Karena itu, tiga developer di atas bertanggung jawab
mengembangkan fitur. Project Manager tidak mengambil alih seluruh
pekerjaan coding.

Tanggung jawab Project Manager: 1. Menjaga repository dan branch utama.
2. Menetapkan baseline migration, nama tabel, kolom, dan relasi yang
sudah tersedia. 3. Meninjau migration tambahan sebelum merge. 4.
Menyetujui perubahan aturan bisnis dan hak akses. 5. Mengatur prioritas
dan dependensi pekerjaan. 6. Melakukan review Pull Request. 7. Membantu
menyelesaikan konflik integrasi. 8. Memastikan aplikasi berjalan pada
branch `develop`. 9. Mengatur uji penerimaan bersama mitra. 10.
Memastikan kontribusi setiap anggota tercatat melalui Issue, commit,
Pull Request, dan hasil pengujian.

Project Manager tidak perlu membuat ulang halaman kasir, dashboard
owner, atau halaman stok. Modul tersebut menjadi tanggung jawab
developer yang tercantum.

------------------------------------------------------------------------

# 2. Persiapan Wajib Seluruh Anggota

Sebelum coding, setiap anggota wajib:

1.  Membaca `README.md` dan dokumen ini.
2.  Clone repository kelompok.
3.  Memeriksa versi Laravel dengan `php artisan --version`.
4.  Memeriksa migration, model, relasi, dan route yang sudah tersedia.
5.  Mengatur `.env` untuk database lokal.
6.  Menjalankan migration dan seeder pada database lokal.
7.  Menjalankan aplikasi dan memastikan baseline tidak error.
8.  Membuat feature branch dari `develop`.
9.  Menggunakan GitHub Issue untuk mencatat pekerjaan.
10. Mengonfirmasi kepada Project Manager bahwa project dapat berjalan di
    komputer masing-masing.

**Jangan:** - Membuat project Laravel baru. - Menghapus migration yang
sudah ada. - Mengubah nama tabel atau kolom tanpa persetujuan. -
Mengubah dependency utama tanpa persetujuan. - Mengunggah `.env`,
password, token, atau API key. - Mengunggah folder `vendor` dan
`node_modules`. - Coding langsung pada `main` atau `develop`. - Mengubah
file anggota lain tanpa koordinasi. - Menganggap fitur selesai hanya
karena tampilannya sudah muncul.

------------------------------------------------------------------------

# 3. ADHITYA ARIF ATAHILLAH

## POS, Transaksi, Laporan Penjualan Harian Karyawan, dan Serah Terima Kas

### 3.1 Tujuan

Adhitya membangun alur yang digunakan karyawan ketika melayani
pelanggan. Fokus utamanya adalah mempercepat transaksi dan mengurangi
kesalahan ketika pelanggan mengganti atau menghapus pesanan.

Adhitya juga membangun laporan penjualan harian karyawan sebagai dasar
pencocokan uang tunai yang diserahkan kepada owner.

### 3.2 Modul A: Halaman Kasir Tailwind

Buat halaman kasir menggunakan Laravel Blade dan Tailwind CSS.

Komponen minimum: 1. Header aplikasi dan informasi pengguna yang login.
2. Daftar barang aktif yang dapat dijual. 3. Kolom pencarian barang. 4.
Filter kategori. 5. Nama barang dan harga. 6. Tombol tambah ke
keranjang. 7. Panel keranjang. 8. Kontrol penambahan dan pengurangan
jumlah. 9. Tombol mengganti atau menghapus barang. 10. Subtotal setiap
barang. 11. Total pembayaran. 12. Pilihan metode pembayaran. 13. Input
nominal diterima untuk tunai. 14. Informasi kembalian. 15. Tombol proses
pembayaran. 16. Ringkasan transaksi setelah berhasil.

Desain harus mengutamakan kecepatan. Karyawan tidak perlu membuka
halaman baru untuk setiap perubahan jumlah barang.

### 3.3 Modul B: Interaksi Keranjang

Keranjang harus mendukung: - Menambah barang. - Menambah jumlah. -
Mengurangi jumlah. - Mengganti barang. - Menghapus barang. - Menghitung
subtotal. - Menghitung total.

Gunakan JavaScript untuk memperbarui tampilan. Perhitungan final tetap
dilakukan di backend.

Aturan: - Browser hanya mengirim ID barang dan jumlah. - Server
mengambil harga dari database. - Server menghitung ulang subtotal dan
total. - Barang nonaktif atau tidak dijual tidak boleh diproses. -
Jumlah harus lebih besar dari nol. - Stok harus mencukupi. - Tombol
pembayaran mencegah submit berulang. - Jika transaksi gagal, stok tidak
boleh berkurang.

### 3.4 Modul C: Proses Pembayaran

Sediakan tiga metode:

``` text
tunai
qris
gopay
```

**Tunai:** 1. Sistem menampilkan total. 2. Karyawan memasukkan nominal
diterima. 3. Sistem memastikan nominal tidak kurang dari total. 4.
Sistem menghitung kembalian. 5. Transaksi disimpan setelah pembayaran
diterima.

**QRIS dan GoPay:** 1. Karyawan memilih metode. 2. Karyawan
memverifikasi pembayaran melalui perangkat pembayaran. 3. Karyawan
menyelesaikan transaksi. 4. Sistem mencatat metode dan total. 5.
Kembalian bernilai nol.

Integrasi payment gateway tidak termasuk versi pertama.

### 3.5 Modul D: Penyimpanan Transaksi

Simpan informasi: - User yang memproses transaksi. - Kode transaksi. -
Tanggal dan waktu. - Metode pembayaran. - Total. - Nominal diterima jika
tunai. - Kembalian. - Status transaksi. - Detail barang. - Nama barang
saat transaksi. - Harga satuan saat transaksi. - Jumlah. - Subtotal.

Gunakan `DB::transaction()` agar transaksi dan detail tersimpan
konsisten.

Jangan mengambil `user_id` dari browser. Gunakan pengguna yang sedang
login.

Simpan nama dan harga saat transaksi sebagai snapshot. Perubahan harga
di masa depan tidak boleh mengubah riwayat transaksi.

### 3.6 Modul E: Laporan Penjualan Harian Karyawan

Buat halaman Tailwind yang hanya menampilkan transaksi milik pengguna
yang sedang login.

Informasi minimum: - Tanggal laporan. - Jumlah transaksi selesai. -
Total penjualan. - Total tunai. - Total QRIS. - Total GoPay. - Uang
tunai yang harus diserahkan. - Uang aktual yang diserahkan. - Selisih. -
Status serah terima.

Tambahkan filter tanggal.

**Keamanan:** query laporan karyawan wajib menggunakan identitas
pengguna yang sedang login. Jangan mengambil data berdasarkan `user_id`
bebas dari URL atau request.

### 3.7 Modul F: Serah Terima Uang Tunai

Rumus:

``` text
uang_tunai_diharapkan = SUM(total transaksi tunai berstatus selesai)
selisih = uang_diserahkan - uang_tunai_diharapkan
```

Jangan menjumlahkan `nominal_diterima`, karena nilai tersebut dapat
mencakup uang kembalian.

Alur: 1. Karyawan membuka laporan harian. 2. Sistem menghitung total
tunai. 3. Karyawan menghitung uang fisik. 4. Karyawan menginput uang
aktual. 5. Server menghitung selisih. 6. Karyawan mengirim laporan. 7.
Sistem menyimpan status `menunggu_verifikasi`. 8. Karyawan tidak dapat
mengubah laporan setelah dikirim. 9. Owner memeriksa dan memverifikasi
melalui Filament.

Jika tabel belum tersedia, buat migration pendukung `serah_terima_kas`.
Jangan mengubah migration baseline yang sudah dijalankan bersama.

Kolom minimum: - `id` - `user_id` - `tanggal_laporan` -
`jumlah_transaksi_tunai` - `uang_tunai_diharapkan` - `uang_diserahkan` -
`selisih` - `diterima_oleh` - `status` - `catatan` - `diserahkan_pada` -
`diverifikasi_pada` - timestamps

Gunakan foreign key ke `users` untuk karyawan dan admin penerima.
Terapkan unique constraint pada kombinasi `user_id` dan
`tanggal_laporan` untuk mendukung satu laporan per karyawan per hari.

Adhitya mengajukan rancangan migration kepada Project Manager untuk
review sebelum merge.

### 3.8 File dan Area Kerja

Struktur yang disarankan:

``` text
app/
├── Http/
│   └── Controllers/
│       ├── KasirController.php
│       ├── TransaksiController.php
│       └── LaporanHarianController.php
├── Models/
│   └── SerahTerimaKas.php
└── Services/
    ├── TransaksiService.php
    └── SerahTerimaKasService.php

database/
└── migrations/
    └── create_serah_terima_kas_table.php

resources/
└── views/
    ├── kasir/
    ├── transaksi/
    └── karyawan/
        └── laporan-harian/

routes/
├── kasir.php
└── laporan-karyawan.php

tests/
└── Feature/
    ├── KasirTest.php
    ├── TransaksiTest.php
    └── LaporanHarianTest.php
```

Periksa file yang sudah tersedia sebelum membuat file baru. Jangan
membuat duplikat controller, model, atau route.

### 3.9 Kriteria Penerimaan Adhitya

-   Halaman kasir dapat dibuka admin dan karyawan.
-   Barang aktif tampil dan dapat dicari.
-   Keranjang dapat ditambah, dikurangi, diganti, dan dihapus.
-   Total dihitung backend.
-   Pembayaran tunai menghitung kembalian.
-   QRIS dan GoPay tidak menghasilkan kembalian.
-   Transaksi dan detail tersimpan.
-   Transaksi mencatat user yang sedang login.
-   Transaksi gagal tidak mengurangi stok.
-   Laporan karyawan hanya menampilkan data miliknya.
-   Perhitungan uang tunai sesuai rumus.
-   Karyawan dapat mengirim laporan serah terima.
-   Karyawan tidak dapat mengubah laporan yang sudah dikirim.
-   Test berhasil dan Pull Request telah direview.

### 3.10 Pengujian Wajib Adhitya

1.  Tambah barang ke keranjang.
2.  Ubah jumlah barang menjadi dua.
3.  Kurangi jumlah menjadi satu.
4.  Hapus barang.
5.  Ganti barang.
6.  Bayar tunai dengan uang pas.
7.  Bayar tunai dengan uang lebih.
8.  Coba nominal tunai kurang.
9.  Bayar QRIS.
10. Bayar GoPay.
11. Tekan tombol pembayaran berulang kali.
12. Pastikan transaksi gagal tidak mengurangi stok.
13. Pastikan karyawan A tidak melihat transaksi karyawan B.
14. Pastikan QRIS dan GoPay tidak masuk total serah terima tunai.
15. Pastikan transaksi batal tidak masuk laporan.
16. Coba mengirim serah terima dua kali pada tanggal yang sama.
17. Pastikan selisih dihitung oleh server.

------------------------------------------------------------------------

# 4. RAFIF RALIANSHAH ZIHNI

## Filament Dashboard Owner, Laporan Penjualan, Otorisasi Admin, dan Verifikasi Kas

### 4.1 Tujuan

Rafif membangun dashboard owner menggunakan Filament. Dashboard membantu
pemilik melihat kondisi penjualan dan memeriksa laporan uang yang
diserahkan karyawan.

Filament digunakan untuk dashboard owner. Halaman kasir dan manajemen
stok tetap menggunakan Tailwind.

### 4.2 Modul A: Setup Filament Panel

1.  Periksa apakah Filament sudah terpasang.
2.  Periksa versi Filament dan kompatibilitas dengan Laravel 13.43.0.
3.  Jika belum terpasang, koordinasikan instalasi dengan Project
    Manager.
4.  Gunakan panel khusus owner.
5.  Tentukan path panel, misalnya `/admin`, sesuai konfigurasi project.
6.  Pastikan hanya role `admin` yang dapat masuk.
7.  Karyawan harus ditolak meskipun membuka URL panel secara langsung.

Jangan mengubah versi dependency utama tanpa persetujuan Project
Manager.

### 4.3 Modul B: Otorisasi Role

Pastikan: - Admin dapat mengakses Filament. - Karyawan tidak dapat
mengakses Filament. - Pengguna belum login diarahkan ke autentikasi. -
Karyawan tidak dapat melihat laporan owner melalui endpoint lain. - Role
tidak dapat diubah melalui profil karyawan. - Hak akses diperiksa di
server.

Gunakan mekanisme otorisasi Filament yang sesuai dengan versi terpasang.
Jika membuat middleware atau policy, dokumentasikan penggunaannya.

### 4.4 Modul C: Dashboard Owner

Buat widget berikut:

1.  **Pendapatan hari ini:** total transaksi berstatus selesai pada
    tanggal berjalan.
2.  **Jumlah transaksi:** jumlah transaksi selesai.
3.  **Penjualan tunai:** total transaksi tunai.
4.  **Penjualan QRIS:** total transaksi QRIS.
5.  **Penjualan GoPay:** total transaksi GoPay.
6.  **Stok menipis:** daftar barang yang mencapai atau berada di bawah
    batas minimum.
7.  **Transaksi terbaru:** kode, tanggal, karyawan, metode pembayaran,
    dan total.
8.  **Penjualan per karyawan:** jumlah transaksi dan total penjualan
    setiap karyawan.
9.  **Serah terima menunggu verifikasi:** jumlah laporan yang belum
    diperiksa.
10. **Selisih serah terima:** laporan dengan kondisi sesuai, lebih, atau
    kurang.

Widget harus membaca database secara dinamis. Jangan menggunakan angka
statis.

### 4.5 Modul D: Filter Laporan

Owner dapat memilih: - Hari ini. - Kemarin. - Rentang tanggal.

Seluruh widget dan tabel harus menggunakan filter tanggal yang
konsisten.

### 4.6 Modul E: Verifikasi Serah Terima Kas

Buat halaman atau tabel Filament untuk menampilkan: - Tanggal laporan. -
Nama karyawan. - Jumlah transaksi tunai. - Uang tunai yang diharapkan. -
Uang aktual yang diserahkan. - Selisih. - Status. - Waktu penyerahan.

Detail laporan menampilkan catatan karyawan.

Aksi admin: 1. Membuka detail laporan. 2. Memeriksa uang yang diharapkan
dan aktual. 3. Memeriksa selisih. 4. Mengonfirmasi penerimaan. 5.
Menyimpan ID admin yang memverifikasi. 6. Menyimpan waktu verifikasi. 7.
Mengubah status menjadi `terverifikasi`.

Admin tidak boleh memverifikasi ulang laporan yang sudah terverifikasi
tanpa prosedur koreksi yang disetujui. Karyawan tidak boleh menjalankan
aksi verifikasi.

### 4.7 Modul F: Laporan Owner

Owner dapat melihat: - Total pendapatan. - Jumlah transaksi. - Total per
metode pembayaran. - Penjualan per karyawan. - Daftar transaksi. -
Daftar serah terima. - Selisih kas.

Sediakan filter tanggal dan pagination.

### 4.8 File dan Area Kerja

Struktur yang disarankan:

``` text
app/
└── Filament/
    ├── Pages/
    │   └── OwnerDashboard.php
    └── Widgets/
        ├── PendapatanHariIni.php
        ├── JumlahTransaksi.php
        ├── RekapMetodePembayaran.php
        ├── StokMenipis.php
        ├── TransaksiTerbaru.php
        ├── PenjualanPerKaryawan.php
        └── SerahTerimaMenungguVerifikasi.php

app/
├── Policies/
└── Http/
    └── Middleware/

tests/
└── Feature/
    ├── FilamentAccessTest.php
    ├── OwnerDashboardTest.php
    └── CashHandoverVerificationTest.php
```

Lokasi file Filament harus mengikuti struktur yang dihasilkan versi
Filament di repository. Jangan memaksakan struktur versi lama.

### 4.9 Kriteria Penerimaan Rafif

-   Panel Filament dapat dibuka admin.
-   Karyawan ditolak ketika mengakses panel.
-   Widget menampilkan data database yang benar.
-   Filter tanggal bekerja.
-   Total metode pembayaran tidak tercampur.
-   Transaksi batal tidak dihitung.
-   Owner dapat melihat data seluruh karyawan.
-   Owner dapat melihat laporan serah terima.
-   Owner dapat memverifikasi laporan.
-   Identitas admin dan waktu verifikasi tersimpan.
-   Laporan terverifikasi tidak dapat diverifikasi ulang sembarangan.
-   Test berhasil dan Pull Request telah direview.

### 4.10 Pengujian Wajib Rafif

1.  Login sebagai admin dan buka Filament.
2.  Login sebagai karyawan dan coba membuka URL Filament.
3.  Bandingkan pendapatan dashboard dengan transaksi.
4.  Bandingkan total tunai, QRIS, dan GoPay.
5.  Uji filter hari ini dan tanggal lain.
6.  Pastikan transaksi batal tidak dihitung.
7.  Pastikan owner melihat beberapa karyawan.
8.  Buka laporan yang menunggu verifikasi.
9.  Verifikasi laporan.
10. Pastikan ID admin dan waktu verifikasi tersimpan.
11. Pastikan laporan terverifikasi tidak dapat diubah sembarangan.

------------------------------------------------------------------------

# 5. GADING RANGGA PAMUNGKAS

## Manajemen Stok Tailwind, Pembaruan Stok Karyawan, dan Peringatan Persediaan

### 5.1 Tujuan

Gading membangun halaman manajemen stok menggunakan Blade dan Tailwind
CSS.

Halaman digunakan admin dan karyawan. Karyawan boleh memperbarui jumlah
stok, tetapi tidak boleh mengubah harga dan identitas barang.

### 5.2 Modul A: Halaman Daftar Stok

Informasi: - Nama barang. - Kategori. - Stok saat ini. - Satuan. - Stok
minimum. - Status barang. - Waktu pembaruan terakhir.

Fungsi: - Pencarian nama. - Filter kategori. - Filter status stok. -
Pagination. - Indikator stok normal. - Indikator stok menipis. -
Indikator stok habis.

Gunakan Tailwind CSS, bukan Filament Resource.

### 5.3 Modul B: Form Pembaruan Stok

Admin dan karyawan dapat memperbarui jumlah stok.

Form: - Nama barang. - Stok saat ini. - Jumlah stok baru atau perubahan
jumlah. - Satuan. - Alasan pembaruan.

Alasan: - Barang masuk. - Hasil pemeriksaan stok. - Koreksi
pencatatan. - Barang rusak atau hilang. - Lainnya.

Validasi: - Stok tidak boleh negatif. - Nilai harus numerik. - Jumlah
sesuai satuan barang. - Barang harus dapat dikelola. - Karyawan tidak
boleh mengubah harga, nama, kategori, atau status aktif. - Identitas
pengguna diambil dari session login.

### 5.4 Modul C: Hak Akses

Karyawan: - Melihat stok. - Memperbarui jumlah stok. - Mengisi alasan
pembaruan. - Melihat status stok minimum. - Tidak mengubah harga. -
Tidak menghapus barang. - Tidak mengubah kategori.

Admin: - Semua hak karyawan. - Mengubah nama barang. - Mengubah
kategori. - Mengubah harga. - Mengubah satuan. - Mengubah stok
minimum. - Mengaktifkan atau menonaktifkan barang.

Pembatasan harus diterapkan di backend, bukan hanya menyembunyikan
tombol.

### 5.5 Modul D: Peringatan Stok Minimum

Aturan:

``` text
stok menipis jika stok <= stok_minimum
stok habis jika stok = 0
```

Contoh:

  Barang          Stok   Minimum Status
  ------------- ------ --------- ---------
  Air Mineral        2         5 Menipis
  Mie Instan         0         5 Habis
  Kopi Hitam        20         5 Normal

Daftar peringatan harus berasal dari database.

### 5.6 Modul E: Konsistensi dengan Transaksi

Koordinasikan dengan Adhitya agar: - Stok produk berkurang setelah
transaksi berhasil. - Transaksi gagal tidak mengurangi stok. - Barang
tidak dapat dijual jika stok tidak mencukupi. - Pembaruan manual tidak
menimpa perubahan transaksi secara tidak konsisten.

Gading tidak boleh membuat mekanisme pengurangan stok kedua yang
bertentangan dengan service transaksi Adhitya.

### 5.7 Modul F: Catatan Perubahan Stok

Setiap perubahan sebaiknya menyimpan aktor dan waktu jika struktur
database mendukungnya.

Jika database belum menyimpan informasi pengubah stok, ajukan migration
tambahan kepada Project Manager. Jangan mengubah migration awal yang
sudah dijalankan bersama.

Riwayat pergerakan stok lengkap dapat menjadi pengembangan lanjutan jika
disetujui.

### 5.8 Modul Tambahan: Rekomendasi Kategori AI

Kerjakan hanya setelah fitur wajib selesai dan jika integrasi AI
termasuk target praktikum.

Alur: 1. Admin memasukkan nama barang. 2. Admin meminta saran kategori.
3. Backend mengirim permintaan ke service AI. 4. AI mengembalikan
rekomendasi. 5. Admin menerima atau mengubah rekomendasi. 6. Data
disimpan setelah admin menekan tombol simpan.

Ketentuan: - API key disimpan di `.env`. - API key tidak dikirim ke
browser. - Sediakan pilihan kategori manual jika API gagal. - AI tidak
menentukan harga atau stok. - Fitur AI tidak boleh menghambat
penyelesaian manajemen stok.

### 5.9 File dan Area Kerja

``` text
app/
├── Http/
│   └── Controllers/
│       └── StokController.php
└── Services/
    └── StokService.php

resources/
└── views/
    └── stok/
        ├── index.blade.php
        ├── edit.blade.php
        └── partials/
            ├── stock-status.blade.php
            └── stock-form.blade.php

routes/
└── stok.php

tests/
└── Feature/
    ├── StokIndexTest.php
    ├── StockUpdateTest.php
    └── StockAuthorizationTest.php
```

Jika AI dikerjakan:

``` text
app/Services/AiCategoryService.php
```

Periksa file yang tersedia sebelum membuat file baru.

### 5.10 Kriteria Penerimaan Gading

-   Halaman stok dapat dibuka admin dan karyawan.
-   Data barang berasal dari database.
-   Pencarian dan filter bekerja.
-   Pagination bekerja.
-   Status stok normal, menipis, dan habis benar.
-   Admin dapat mengubah data barang sesuai kewenangan.
-   Karyawan dapat memperbarui jumlah stok.
-   Karyawan tidak dapat mengubah harga atau identitas barang.
-   Stok negatif ditolak.
-   Pembaruan tersimpan.
-   Pembaruan tidak merusak proses transaksi.
-   Test berhasil dan Pull Request telah direview.

### 5.11 Pengujian Wajib Gading

1.  Buka halaman stok sebagai admin.
2.  Buka halaman stok sebagai karyawan.
3.  Cari barang berdasarkan nama.
4.  Filter kategori.
5.  Uji pagination.
6.  Ubah stok dari 10 menjadi 15.
7.  Ubah stok menjadi 0.
8.  Masukkan stok negatif.
9.  Coba karyawan mengubah harga melalui UI.
10. Coba karyawan mengubah harga melalui request langsung.
11. Pastikan peringatan stok minimum muncul.
12. Pastikan stok habis ditampilkan terpisah.
13. Uji pembaruan stok saat transaksi diproses.
14. Pastikan stok terbaru terlihat setelah halaman dimuat ulang.

------------------------------------------------------------------------

# 6. Kontrak Integrasi Antaranggota

## 6.1 Database

Migration awal di repository menjadi acuan. Sebelum coding, periksa nama
tabel dan kolom aktual.

Entitas inti: - `users` - `menu_barang` - `transaksi` -
`detail_transaksi`

Relasi: - `users` hasMany `transaksi`. - `transaksi` belongsTo
`users`. - `transaksi` hasMany `detail_transaksi`. - `detail_transaksi`
belongsTo `transaksi`. - `menu_barang` hasMany `detail_transaksi`. -
`detail_transaksi` belongsTo `menu_barang`.

Jangan membuat nama relasi atau foreign key berbeda tanpa persetujuan.

## 6.2 Role

Nilai role:

``` text
admin
karyawan
```

Jangan menggunakan variasi `owner`, `staff`, `employee`, atau `kasir`
sebagai nilai database role.

## 6.3 Metode Pembayaran

Nilai metode pembayaran:

``` text
tunai
qris
gopay
```

Jangan menyimpan variasi bebas seperti `cash`, `QRIS Payment`, atau
`GoPay` jika baseline sudah menetapkan nilai di atas.

## 6.4 Status Transaksi

Nilai yang diharapkan:

``` text
selesai
dibatalkan
```

Cocokkan dengan migration awal. Jika baseline menggunakan nilai berbeda,
Project Manager menetapkan pemetaan sebelum implementasi.

## 6.5 Status Serah Terima

``` text
menunggu_verifikasi
terverifikasi
```

Karyawan membuat laporan. Admin memverifikasi. Karyawan tidak dapat
memverifikasi laporannya sendiri.

## 6.6 Data Kas

Sumber perhitungan: - Hanya transaksi tunai. - Hanya transaksi
selesai. - Hanya tanggal laporan yang dipilih. - Hanya transaksi milik
karyawan untuk laporan karyawan. - Owner dapat melihat seluruh karyawan.

Rumus:

``` text
uang_tunai_diharapkan = SUM(transaksi.total)
selisih = uang_diserahkan - uang_tunai_diharapkan
```

Server menghitung kedua nilai tersebut. Browser hanya mengirim jumlah
aktual dan catatan.

## 6.7 Tampilan

  Halaman                   Teknologi          Pengguna
  ------------------------- ------------------ --------------------
  Kasir                     Blade + Tailwind   Admin dan karyawan
  Manajemen stok            Blade + Tailwind   Admin dan karyawan
  Laporan harian karyawan   Blade + Tailwind   Karyawan
  Dashboard owner           Filament           Admin
  Verifikasi serah terima   Filament           Admin

------------------------------------------------------------------------

# 7. Kepemilikan File dan Pencegahan Konflik

  Area                                         Pemilik utama
  -------------------------------------------- --------------------
  `resources/views/kasir/`                     Adhitya
  `resources/views/transaksi/`                 Adhitya
  `resources/views/karyawan/laporan-harian/`   Adhitya
  `KasirController.php`                        Adhitya
  `TransaksiController.php`                    Adhitya
  `LaporanHarianController.php`                Adhitya
  `TransaksiService.php`                       Adhitya
  `SerahTerimaKasService.php`                  Adhitya
  migration dan model serah terima             Adhitya, review PM
  `app/Filament/Pages/`                        Rafif
  `app/Filament/Widgets/`                      Rafif
  konfigurasi panel Filament                   Rafif
  otorisasi Filament                           Rafif, review PM
  `resources/views/stok/`                      Gading
  `StokController.php`                         Gading
  `StokService.php`                            Gading
  `routes/stok.php`                            Gading
  test inventaris                              Gading

File bersama yang harus dikoordinasikan: - `routes/web.php` -
`bootstrap/app.php` - `app/Models/User.php` - model inti yang sudah
tersedia - migration inti yang sudah dijalankan bersama -
`composer.json` - `package.json`

Jangan mengubah file bersama secara bersamaan. Ajukan perubahan melalui
Pull Request dan beri tahu Project Manager.

Gunakan file route khusus seperti `routes/kasir.php`, `routes/stok.php`,
dan `routes/laporan-karyawan.php` jika diperlukan. Project Manager
mengatur pemuatan file tersebut dari route utama.

------------------------------------------------------------------------

# 8. Urutan Pengerjaan

## Tahap 1 - Pemeriksaan Baseline

Seluruh anggota: - Clone repository. - Install dependency. - Atur
`.env`. - Jalankan migration dan seeder. - Jalankan aplikasi. - Periksa
model, relasi, dan route. - Konfirmasi baseline berjalan kepada Project
Manager.

## Tahap 2 - Pengembangan Paralel

**Adhitya:** - Halaman kasir. - Interaksi keranjang. - Proses
transaksi. - Laporan harian. - Migration/model serah terima jika belum
ada. - Form penyerahan uang.

**Rafif:** - Setup Filament. - Otorisasi admin. - Dashboard owner. -
Widget penjualan. - Tabel serah terima. - Aksi verifikasi.

**Gading:** - Halaman stok. - Pencarian dan filter. - Form pembaruan
stok. - Pembatasan role. - Peringatan stok minimum. - Pengujian
inventaris.

## Tahap 3 - Integrasi

-   Adhitya dan Gading memastikan pengurangan stok transaksi tidak
    bertabrakan dengan pembaruan stok manual.
-   Adhitya dan Rafif menyepakati struktur data serah terima.
-   Rafif memastikan dashboard membaca data yang sama dengan laporan
    karyawan.
-   Project Manager meninjau migration dan aturan bisnis.
-   Semua anggota membuat Pull Request ke `develop`.

## Tahap 4 - Pengujian Bersama

Gunakan skenario data yang sama:

1.  Karyawan A melakukan transaksi tunai Rp50.000.
2.  Karyawan A melakukan transaksi QRIS Rp30.000.
3.  Karyawan B melakukan transaksi tunai Rp20.000.
4.  Karyawan A membuka laporan harian.
5.  Sistem menampilkan uang tunai yang diharapkan Rp50.000, bukan
    Rp80.000.
6.  Karyawan A menyerahkan uang aktual Rp50.000.
7.  Sistem mencatat selisih Rp0.
8.  Owner membuka Filament.
9.  Owner memverifikasi laporan Karyawan A.
10. Owner dapat melihat laporan Karyawan B secara terpisah.

Hasil laporan karyawan dan dashboard owner harus konsisten.

## Tahap 5 - Penyelesaian

-   Perbaiki bug.
-   Pastikan test berhasil.
-   Pastikan `npm run build` berhasil.
-   Periksa hak akses.
-   Perbarui dokumentasi.
-   Merge fitur ke `develop`.
-   Lakukan uji penerimaan bersama mitra.
-   Merge ke `main` setelah Project Manager menyetujui versi stabil.

------------------------------------------------------------------------

# 9. Aturan GitHub

Branch:

``` text
main
develop

feature/kasir-transaksi
feature/laporan-harian-karyawan
feature/serah-terima-kas

feature/filament-owner-dashboard
feature/owner-cash-verification

feature/manajemen-stok
feature/stock-adjustment
```

Alur kerja:

``` bash
git switch develop
git pull origin develop
git switch -c feature/nama-fitur

# Coding dan pengujian

git add .
git commit -m "feat: deskripsi fitur"
git push -u origin feature/nama-fitur
```

Buat Pull Request menuju `develop`.

Contoh commit:

``` text
feat(kasir): implement cart management
feat(transaction): save transaction details
feat(report): add employee daily report
feat(cash): add cash handover submission
feat(filament): create owner dashboard
feat(filament): add cash handover verification
feat(inventory): implement stock update
feat(inventory): add low stock indicator
fix(auth): restrict employee dashboard access
test: add inventory authorization tests
```

Satu Pull Request sebaiknya berisi satu kelompok pekerjaan yang saling
terkait. Hindari Pull Request yang mencampur kasir, stok, dashboard, dan
perubahan database yang tidak berhubungan.

## Template GitHub Issue

``` text
TITLE:
[MODUL] Nama pekerjaan

TUJUAN:
Jelaskan kebutuhan yang ingin diselesaikan.

RUANG LINGKUP:
- Pekerjaan 1
- Pekerjaan 2
- Pekerjaan 3

FILE YANG TERKAIT:
- Daftar file yang dibuat atau diubah

ATURAN BISNIS:
- Aturan yang harus dipatuhi

KRITERIA SELESAI:
- [ ] Fitur berjalan
- [ ] Validasi tersedia
- [ ] Hak akses diuji
- [ ] Test berhasil
- [ ] Screenshot tersedia
- [ ] Pull Request dibuat

PENANGGUNG JAWAB:
Nama anggota

DEPENDENSI:
Fitur atau anggota lain yang harus selesai lebih dahulu.
```

## Template Pull Request

``` text
JUDUL:
feat: nama fitur

RINGKASAN:
Jelaskan perubahan.

FITUR:
- [ ] Fitur A
- [ ] Fitur B

DATABASE:
- [ ] Tidak ada perubahan database
- [ ] Ada migration baru (jelaskan)

PENGUJIAN:
- [ ] php artisan test
- [ ] npm run build
- [ ] Pengujian manual
- [ ] Pengujian hak akses

BUKTI:
Lampirkan screenshot atau hasil pengujian.

CATATAN:
Jelaskan risiko atau pekerjaan lanjutan.
```

------------------------------------------------------------------------

# 10. Checklist Akhir Per Anggota

## Adhitya

-   [ ] Halaman kasir Tailwind selesai.
-   [ ] Pencarian dan filter barang selesai.
-   [ ] Keranjang dapat diubah.
-   [ ] Pembayaran tunai, QRIS, dan GoPay berjalan.
-   [ ] Transaksi dan detail tersimpan.
-   [ ] Stok berkurang sesuai transaksi.
-   [ ] Laporan harian karyawan selesai.
-   [ ] Perhitungan uang tunai benar.
-   [ ] Serah terima dapat dikirim.
-   [ ] Karyawan tidak dapat mengubah laporan setelah dikirim.
-   [ ] Test berhasil.
-   [ ] Pull Request di-merge.

## Rafif

-   [ ] Filament panel siap.
-   [ ] Hanya admin dapat mengakses panel.
-   [ ] Widget pendapatan selesai.
-   [ ] Widget jumlah transaksi selesai.
-   [ ] Rekap metode pembayaran selesai.
-   [ ] Peringatan stok muncul.
-   [ ] Transaksi terbaru tampil.
-   [ ] Ringkasan per karyawan tampil.
-   [ ] Laporan serah terima tampil.
-   [ ] Verifikasi owner berjalan.
-   [ ] Identitas dan waktu verifikasi tersimpan.
-   [ ] Test berhasil.
-   [ ] Pull Request di-merge.

## Gading

-   [ ] Halaman stok Tailwind selesai.
-   [ ] Pencarian barang selesai.
-   [ ] Filter kategori selesai.
-   [ ] Pagination selesai.
-   [ ] Form pembaruan stok selesai.
-   [ ] Karyawan dapat memperbarui jumlah stok.
-   [ ] Karyawan tidak dapat mengubah harga dan identitas barang.
-   [ ] Peringatan stok minimum selesai.
-   [ ] Status stok habis tampil.
-   [ ] Integrasi dengan transaksi diuji.
-   [ ] Test berhasil.
-   [ ] Pull Request di-merge.

------------------------------------------------------------------------

# 11. Standar Hasil Akhir

Proyek belum selesai hanya karena halaman dapat dibuka.

Setiap modul harus memenuhi empat aspek:

1.  **Fungsional:** alur sesuai kebutuhan mitra.
2.  **Data:** informasi tersimpan dan dihitung dengan benar.
3.  **Keamanan:** role dan kepemilikan data diperiksa di server.
4.  **Kolaborasi:** kode masuk melalui commit dan Pull Request yang
    dapat ditinjau.

Jika dokumen ini berbeda dengan migration yang sudah ada, jangan
mengubah database sendiri. Catat perbedaannya pada GitHub Issue dan
minta keputusan Project Manager.

Dokumen ini menjadi acuan pembagian kerja sampai Project Manager
menyetujui perubahan ruang lingkup.
