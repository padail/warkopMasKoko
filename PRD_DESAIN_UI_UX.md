# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Standar Desain UI/UX dan Design System
### Warkop Management System - Warkop Mas Koko

**Versi:** 1.0  
**Status:** Acuan desain tim  
**Framework:** Laravel 13.43.0  
**Antarmuka:** Blade + Tailwind CSS dan Filament  
**Pengguna:** Admin (owner) dan Karyawan  
**Pemilik dokumen:** Project Manager

---

## 1. Tujuan Dokumen

Dokumen ini menetapkan satu standar visual dan interaksi untuk seluruh halaman Warkop Management System. Semua anggota tim wajib mengikuti token desain, komponen, ukuran, pola layout, dan aturan responsif yang sama.

Dokumen ini berlaku untuk:
- Halaman kasir.
- Halaman manajemen stok.
- Laporan penjualan harian karyawan.
- Dashboard owner berbasis Filament.
- Form, tabel, modal, notifikasi, navigasi, dan komponen pendukung.

**Aturan utama:** developer tidak boleh menentukan warna, font, ukuran, radius, atau gaya komponen sendiri untuk satu halaman. Gunakan standar pada dokumen ini. Jika ada kebutuhan yang belum diatur, ajukan keputusan kepada Project Manager sebelum membuat variasi.

## 2. Latar Belakang dan Masalah

Tiga developer mengerjakan modul yang berbeda. Tanpa acuan desain bersama, hasil implementasi berisiko memiliki:
- Warna tombol dan latar yang tidak sama.
- Ukuran heading dan teks yang berubah antarhalaman.
- Tinggi input dan tombol yang tidak konsisten.
- Radius card dan form yang berbeda.
- Spacing dan lebar konten yang tidak seragam.
- Pola tabel, badge, dan notifikasi yang berbeda.
- Perbedaan gaya antara halaman Tailwind dan Filament.

PRD ini mencegah fragmentasi visual dan menetapkan standar yang dapat diterapkan pada kedua teknologi.

## 3. Sasaran Produk

1. Membuat seluruh halaman terasa sebagai satu aplikasi.
2. Mengurangi keputusan desain individual yang tidak konsisten.
3. Membuat antarmuka kasir cepat dipahami dan digunakan.
4. Membuat informasi stok mudah dipindai.
5. Membuat laporan karyawan mudah dicocokkan dengan uang tunai.
6. Membuat dashboard owner mudah dibaca dan dipantau.
7. Memastikan tampilan tetap nyaman pada desktop, tablet, dan mobile.
8. Memudahkan Project Manager melakukan review visual melalui Pull Request.

### 3.1 Indikator keberhasilan

- Seluruh halaman memakai font, warna, dan skala ukuran yang sama.
- Semua tombol utama, input, card, tabel, badge, dan modal mengikuti spesifikasi.
- Tidak ada warna atau ukuran acak di luar token tanpa persetujuan.
- Seluruh halaman inti lolos checklist visual pada bagian 15.
- Tampilan desktop dan mobile tidak memiliki overflow horizontal yang tidak diperlukan.
- Perbedaan Blade dan Filament hanya mengikuti kebutuhan framework, bukan perbedaan identitas visual.

## 4. Pengguna dan Konteks Penggunaan

| Pengguna | Halaman utama | Kebutuhan UI |
|---|---|---|
| Admin / Owner | Filament Dashboard | Ringkasan penjualan, monitoring, verifikasi kas, dan informasi stok |
| Admin / Owner | Halaman Tailwind | Kasir dan pengelolaan stok |
| Karyawan | Halaman Tailwind | Kasir, pembaruan stok, laporan harian, dan pengiriman serah terima |

### 4.1 Prinsip desain

- **Jelas:** label dan aksi harus mudah dimengerti.
- **Konsisten:** satu jenis komponen memiliki tampilan dan perilaku yang sama.
- **Efisien:** halaman kasir meminimalkan langkah dan perpindahan halaman.
- **Terbaca:** informasi nominal, stok, status, dan tanggal mudah dipindai.
- **Responsif:** layout menyesuaikan ukuran layar.
- **Aman:** UI tidak menggantikan validasi dan otorisasi backend.

## 5. Arah Visual

**Nama konsep:** Warm Coffee Minimalist.

Karakter:
- Latar terang bernuansa krem.
- Warna utama cokelat kopi.
- Aksen amber untuk penekanan dan aksi penting.
- Card putih dengan border lembut.
- Tipografi sans-serif yang bersih.
- Ikon sederhana dan konsisten.
- Minim dekorasi yang tidak membantu pekerjaan.

Jangan menggunakan tekstur kayu, gambar biji kopi, gradasi mencolok, neon, atau dekorasi berlebihan pada dashboard operasional. Identitas warkop cukup ditampilkan melalui palet warna dan elemen merek yang sederhana.

## 6. Design Tokens

Semua warna dan ukuran harus menggunakan token di bawah ini. Jika implementasi memakai Tailwind, gunakan nama token yang sama atau padanan yang disepakati.

### 6.1 Palet warna

| Token | Hex | Fungsi |
|---|---|---|
| `brand-900` | `#3B2A20` | Cokelat gelap, sidebar/teks brand kuat |
| `brand-700` | `#6F4E37` | Warna utama dan tombol primer |
| `brand-600` | `#805B42` | Hover tombol primer |
| `brand-100` | `#EFE7DF` | Latar lembut elemen brand |
| `accent-500` | `#D6A354` | Aksen amber dan penanda pilihan |
| `accent-100` | `#F8EEDB` | Latar aksen |
| `page` | `#F7F5F1` | Latar utama halaman |
| `surface` | `#FFFFFF` | Card, panel, modal, input |
| `surface-muted` | `#F1EEE8` | Latar tabel/area sekunder |
| `text-primary` | `#26221E` | Teks utama |
| `text-secondary` | `#70685F` | Deskripsi dan teks sekunder |
| `text-disabled` | `#A8A29B` | Teks nonaktif |
| `border` | `#E4DED6` | Border dan divider |
| `success` | `#237A57` | Status berhasil/terverifikasi |
| `success-bg` | `#E8F4ED` | Latar status berhasil |
| `warning` | `#A86412` | Stok menipis/perlu perhatian |
| `warning-bg` | `#FFF3DC` | Latar status peringatan |
| `danger` | `#B42318` | Error, stok habis, aksi destruktif |
| `danger-bg` | `#FDECEC` | Latar status error |
| `info` | `#245B9A` | Informasi umum |
| `info-bg` | `#EAF2FB` | Latar informasi |

Aturan penggunaan:
- Gunakan `brand-700` untuk tombol primer.
- Gunakan `brand-900` untuk teks brand dan elemen navigasi gelap.
- Gunakan `accent-500` secara terbatas. Jangan menjadikannya warna semua tombol.
- Gunakan `success`, `warning`, `danger`, dan `info` hanya untuk status yang sesuai.
- Jangan menyampaikan status hanya melalui warna. Sertakan teks atau ikon.
- Pastikan teks memiliki kontras yang cukup terhadap latar.

### 6.2 Tipografi

**Font utama:** Inter.  
**Fallback:** `ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`.

Gunakan satu font untuk Blade dan Filament. Jika Inter tidak tersedia secara lokal, fallback harus tetap konsisten. Hindari mencampur Poppins, Roboto, Arial, dan font dekoratif pada halaman berbeda.

| Token | Ukuran | Line height | Weight | Penggunaan |
|---|---:|---:|---:|---|
| `text-xs` | 12 px | 16 px | 400/500 | Metadata, helper |
| `text-sm` | 14 px | 20 px | 400/500 | Label, tabel, deskripsi |
| `text-base` | 16 px | 24 px | 400 | Isi utama dan input |
| `text-lg` | 18 px | 28 px | 600 | Subjudul/card title |
| `text-xl` | 20 px | 28 px | 600 | Judul section |
| `text-2xl` | 24 px | 32 px | 700 | Judul halaman |
| `text-3xl` | 30 px | 36 px | 700 | Angka KPI utama, bila diperlukan |

Aturan:
- Judul halaman selalu 24 px, weight 700.
- Judul card 18 px, weight 600.
- Isi normal 14 atau 16 px sesuai kepadatan informasi.
- Label form 14 px, weight 500.
- Teks tabel 14 px.
- Angka nominal penting dapat menggunakan 20 atau 24 px.
- Jangan menggunakan lebih dari tiga tingkat ukuran dalam satu card.

### 6.3 Spacing

Gunakan kelipatan 4 px. Skala umum:

| Token | Nilai |
|---|---:|
| `space-1` | 4 px |
| `space-2` | 8 px |
| `space-3` | 12 px |
| `space-4` | 16 px |
| `space-5` | 20 px |
| `space-6` | 24 px |
| `space-8` | 32 px |
| `space-10` | 40 px |
| `space-12` | 48 px |

Aturan layout:
- Padding halaman desktop: 24 px.
- Padding halaman tablet: 20 px.
- Padding halaman mobile: 16 px.
- Gap antar-card: 16 atau 24 px.
- Padding card standar: 20 atau 24 px.
- Jarak label ke input: 6 atau 8 px.
- Jarak antar-field form: 16 px.
- Jarak antar-section: 24 atau 32 px.

### 6.4 Radius, border, dan shadow

| Token | Nilai | Penggunaan |
|---|---:|---|
| `radius-sm` | 6 px | Badge kecil |
| `radius-md` | 8 px | Input dan tombol |
| `radius-lg` | 12 px | Card dan modal |
| `radius-xl` | 16 px | Panel besar bila dibutuhkan |
| `radius-full` | 999 px | Badge pill/avatar |

Border standar: 1 px solid `#E4DED6`.

Shadow card: sangat ringan. Hindari shadow tebal atau glow. Card standar sebaiknya dibedakan dengan kombinasi surface putih dan border, bukan bayangan kuat.

### 6.5 Ukuran komponen

| Komponen | Standar |
|---|---|
| Tombol small | Tinggi 32 px |
| Tombol medium | Tinggi 40 px |
| Tombol large | Tinggi 44 px |
| Input/select standar | Tinggi minimum 40 px |
| Textarea | Tinggi minimum 88 px |
| Ikon kecil | 16 px |
| Ikon standar | 20 px |
| Ikon besar | 24 px |
| Avatar standar | 32 px |
| Badge | Tinggi sekitar 24 px |
| Baris tabel | Minimum 48 px |
| Header topbar | 64 px |
| Sidebar desktop | 248 px |
| Lebar konten maksimum | 1440 px |

Jangan membuat tombol, input, atau card berbeda ukuran pada halaman yang berbeda kecuali ada alasan usability yang disetujui.

## 7. Layout dan Responsivitas

Gunakan breakpoint Tailwind standar:

| Breakpoint | Lebar minimum | Perilaku |
|---|---:|---|
| `sm` | 640 px | Mobile besar |
| `md` | 768 px | Tablet |
| `lg` | 1024 px | Desktop kecil |
| `xl` | 1280 px | Desktop |
| `2xl` | 1536 px | Desktop lebar |

### 7.1 Desktop

- Konten berada dalam lebar maksimum 1440 px.
- Sidebar Filament sekitar 248 px.
- Topbar sekitar 64 px.
- Padding konten 24 px.
- Grid KPI 4 kolom jika ruang mencukupi.
- Tabel menggunakan lebar penuh area konten.

### 7.2 Tablet

- Kurangi grid menjadi 2 kolom.
- Sidebar dapat berubah menjadi drawer sesuai kemampuan layout.
- Form dua kolom dapat berubah menjadi satu kolom jika ruang sempit.
- Tabel dapat memakai horizontal scroll hanya pada area tabel.

### 7.3 Mobile

- Padding halaman 16 px.
- KPI satu atau dua kolom sesuai lebar.
- Form satu kolom.
- Tombol aksi utama dapat full width.
- Sidebar menjadi drawer atau menu mobile.
- Keranjang kasir ditampilkan sebagai panel terpisah atau drawer.
- Jangan membuat seluruh halaman melebar karena satu tabel.
- Informasi penting tidak boleh tersembunyi di hover.

## 8. Standar Komponen Bersama

### 8.1 Tombol

**Primary:** latar `brand-700`, teks putih, hover `brand-600`.  
**Secondary:** latar putih, teks `text-primary`, border `border`.  
**Tertiary/Ghost:** tanpa latar permanen, teks `brand-700`.  
**Danger:** latar `danger`, teks putih.

Aturan:
- Tinggi default 40 px.
- Radius 8 px.
- Padding horizontal 16 px.
- Font 14 px, weight 600.
- Ikon 16 px dan jarak ikon-teks 8 px.
- Gunakan label aksi yang jelas: Simpan, Proses Pembayaran, Perbarui Stok, Verifikasi.
- Tombol disabled harus terlihat nonaktif dan tidak dapat diklik.

### 8.2 Input, select, dan textarea

- Label di atas field.
- Tinggi input/select minimum 40 px.
- Border `border`.
- Radius 8 px.
- Padding horizontal 12 px.
- Font 14 px.
- Focus ring menggunakan warna brand dengan opacity yang lembut.
- Error ditampilkan di bawah field menggunakan warna danger dan teks 12 px.
- Placeholder memakai `text-disabled`, bukan teks utama.
- Jangan hanya menggunakan placeholder sebagai label.

### 8.3 Card

- Latar putih.
- Border 1 px.
- Radius 12 px.
- Padding 20 atau 24 px.
- Judul 18 px, weight 600.
- Jarak antar elemen 16 px.
- Shadow ringan atau tanpa shadow.

### 8.4 Tabel

- Header memakai `surface-muted`.
- Header teks 12 atau 14 px, weight 600.
- Isi tabel 14 px.
- Tinggi baris minimum 48 px.
- Border bawah antarbaris.
- Angka nominal rata kanan.
- Kolom aksi konsisten di sisi kanan.
- Sediakan empty state, loading state, dan pagination.
- Pada mobile, gunakan tampilan kartu atau scroll di dalam pembungkus tabel.

### 8.5 Badge status

Gunakan pola berikut:

| Status | Warna | Label contoh |
|---|---|---|
| Normal/berhasil | Success | Terverifikasi |
| Menunggu | Warning | Menunggu Verifikasi |
| Stok menipis | Warning | Menipis |
| Stok habis | Danger | Habis |
| Dibatalkan/error | Danger | Dibatalkan |
| Informasi | Info | Diproses |

Badge menggunakan font 12 px, weight 500, padding horizontal 8 px, dan radius full atau 6 px. Label harus menjelaskan status, tidak hanya warna.

### 8.6 Modal dan konfirmasi

- Radius 12 px.
- Padding 24 px.
- Judul 18 px.
- Isi 14 px.
- Tombol utama di sisi kanan pada desktop.
- Aksi destruktif wajib meminta konfirmasi.
- Modal tidak boleh terlalu lebar; gunakan lebar sesuai isi dan maksimum sekitar 480 px untuk konfirmasi sederhana.

### 8.7 Notifikasi

Gunakan toast/alert yang konsisten:
- Berhasil: success.
- Gagal: danger.
- Peringatan: warning.
- Informasi: info.

Pesan harus spesifik. Contoh: “Stok berhasil diperbarui.” Hindari pesan umum seperti “Terjadi sesuatu.”

### 8.8 Ikon

- Gunakan satu keluarga ikon, disarankan Lucide.
- Ukuran standar 20 px.
- Ikon aksi kecil 16 px.
- Jangan mencampur emoji, ikon Font Awesome, dan Lucide pada satu antarmuka.
- Ikon yang tidak jelas harus disertai label atau tooltip.

## 9. Spesifikasi Tampilan per Halaman

### 9.1 Kasir - Blade + Tailwind

Tujuan: mempercepat proses pemesanan dan pembayaran.

Desktop:
- Header halaman dengan judul “Kasir”.
- Area katalog barang di sisi kiri, sekitar 60%.
- Panel keranjang dan pembayaran di sisi kanan, sekitar 40%.
- Search dan filter kategori berada di atas katalog.
- Barang ditampilkan dalam grid card yang seragam.
- Panel keranjang memiliki daftar item, kontrol jumlah, subtotal, dan total.
- Metode pembayaran ditampilkan sebagai pilihan yang mudah dikenali.
- Input nominal diterima hanya aktif untuk tunai.
- Kembalian ditampilkan jelas untuk pembayaran tunai.
- Tombol “Proses Pembayaran” menjadi primary action.

Mobile:
- Katalog dan keranjang tidak dipaksakan berdampingan.
- Keranjang dapat dibuka sebagai drawer atau panel terpisah.
- Total dan aksi pembayaran tetap mudah dijangkau.
- Kontrol jumlah harus memiliki area sentuh memadai.

Aturan visual:
- Harga menggunakan format Rupiah yang konsisten.
- Total pembayaran lebih menonjol daripada subtotal.
- Barang tidak tersedia atau stok habis diberi status yang jelas.
- Jangan mengandalkan warna saja untuk membedakan barang yang tidak dapat dijual.

### 9.2 Manajemen Stok - Blade + Tailwind

Tujuan: membantu admin dan karyawan melihat serta memperbarui stok dengan cepat.

Susunan:
- Judul “Manajemen Stok”.
- Tombol aksi utama sesuai role.
- Search dan filter di area atas.
- Ringkasan jumlah barang, stok menipis, dan stok habis dapat ditampilkan sebagai KPI.
- Tabel berisi nama barang, kategori, stok, satuan, stok minimum, status, dan aksi.
- Form edit/pembaruan menggunakan komponen input standar.
- Alasan perubahan stok disediakan jika diwajibkan alur bisnis.

Aturan visual:
- Stok angka rata kanan atau disajikan konsisten.
- Status menipis memakai warning.
- Status habis memakai danger.
- Status normal memakai success atau label netral.
- Tombol karyawan hanya menampilkan aksi yang diizinkan, tetapi backend tetap wajib memvalidasi role.
- Harga dan identitas barang tidak boleh tampak dapat diedit oleh karyawan.

### 9.3 Laporan Keuangan Karyawan - Blade + Tailwind

Tujuan: membantu karyawan mencatat pemasukan, pengeluaran, saldo, dan
setoran dari transaksi hariannya.

Susunan:
- Judul “Laporan Keuangan”.
- Ringkasan semua tanggal sebagai tampilan awal dan filter tanggal opsional.
- Pemasukan dan pengeluaran dipisahkan menjadi tunai dan non-tunai.
- Saldo bersih tunai, non-tunai, dan keseluruhan.
- Form pencatatan pengeluaran berisi tanggal, kategori, keterangan, nominal, dan metode.
- Riwayat pemasukan dan pengeluaran dengan tanggal dan metode pembayaran.
- Panel setoran harian saat satu tanggal dipilih.
- Form jumlah setoran aktual tunai dan non-tunai.
- Tampilan selisih dan status pengiriman.

Aturan visual:
- Pemasukan, pengeluaran, dan saldo memiliki label yang jelas; warna tidak menjadi satu-satunya penanda.
- QRIS dan GoPay dikelompokkan sebagai pemasukan non-tunai.
- Setoran dan selisih tunai/non-tunai ditampilkan terpisah.
- Karyawan hanya melihat catatan miliknya.

### 9.4 Dashboard Owner - Filament

Tujuan: memberi gambaran operasional dan penjualan kepada owner.

Susunan:
- Sidebar Filament mengikuti warna brand gelap.
- Area konten memakai latar `page`.
- Topbar mengikuti tinggi standar.
- Judul “Dashboard Owner”.
- Filter tanggal berada di area atas.
- KPI pendapatan, transaksi, tunai, QRIS, dan GoPay.
- Grafik penjualan dan ringkasan per karyawan.
- Daftar transaksi terbaru.
- Daftar stok menipis.
- Daftar serah terima yang menunggu verifikasi.

Aturan visual:
- Widget KPI menggunakan card yang sama dengan halaman Tailwind.
- Warna status harus sama dengan token global.
- Format nominal dan tanggal sama dengan halaman karyawan.
- Tabel Filament mengikuti ukuran font, tinggi baris, border, dan badge yang sama.
- Aksi verifikasi menggunakan primary action; aksi penolakan/koreksi menggunakan danger jika memang disediakan alur bisnis.
- Jangan membuat dashboard Filament dengan warna default yang berbeda dari aplikasi.

## 10. Konsistensi Blade dan Filament

Kedua teknologi harus menggunakan token visual yang sama.

| Elemen | Blade + Tailwind | Filament |
|---|---|---|
| Font | Inter | Inter |
| Primary | `brand-700` | `brand-700` |
| Page background | `page` | `page` |
| Surface/card | `surface` | `surface` |
| Border | `border` | `border` |
| Radius card | 12 px | 12 px |
| Radius input/button | 8 px | 8 px |
| Teks utama | `text-primary` | `text-primary` |
| Status success/warning/danger | Token global | Token global |

Rafif bertanggung jawab menyesuaikan tema Filament sesuai versi yang terpasang. RADHITYA dan Gading menggunakan token yang sama pada Blade. Project Manager menyetujui implementasi token global dan integrasi asset.

## 11. Implementasi Design Tokens

Project Manager menetapkan token sebagai sumber tunggal. Developer tidak boleh menyalin hex warna secara acak ke banyak file.

Contoh konfigurasi CSS variable:

```css
:root {
  --color-brand-900: #3B2A20;
  --color-brand-700: #6F4E37;
  --color-brand-600: #805B42;
  --color-brand-100: #EFE7DF;
  --color-accent-500: #D6A354;
  --color-accent-100: #F8EEDB;

  --color-page: #F7F5F1;
  --color-surface: #FFFFFF;
  --color-surface-muted: #F1EEE8;
  --color-text-primary: #26221E;
  --color-text-secondary: #70685F;
  --color-text-disabled: #A8A29B;
  --color-border: #E4DED6;

  --color-success: #237A57;
  --color-success-bg: #E8F4ED;
  --color-warning: #A86412;
  --color-warning-bg: #FFF3DC;
  --color-danger: #B42318;
  --color-danger-bg: #FDECEC;
  --color-info: #245B9A;
  --color-info-bg: #EAF2FB;

  --font-sans: Inter, ui-sans-serif, system-ui, sans-serif;

  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
}
```

Contoh pemakaian pada Blade:

```html
<div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
    ...
</div>
```

Contoh ini menunjukkan penggunaan token. Implementasi akhir harus mengikuti konfigurasi Tailwind dan Vite yang sudah ada. Jangan menambah konfigurasi duplikat.

### 11.1 Font

Gunakan satu sumber font yang disepakati. Prioritaskan file font lokal atau dependency yang sudah tersedia. Jika menggunakan font eksternal, Project Manager harus menyetujui cara pemuatan dan fallback-nya. Filament harus menggunakan font yang sama.

### 11.2 Filament theme

Rafif wajib:
- Memeriksa versi Filament aktual.
- Mengikuti dokumentasi versi tersebut untuk custom theme.
- Mengatur warna primary sesuai `brand-700`.
- Menyamakan font dan radius.
- Menyesuaikan warna surface, border, dan status.
- Menyediakan screenshot dashboard setelah perubahan.

Jangan menyalin instruksi konfigurasi Filament dari versi berbeda tanpa memeriksa kompatibilitas.

## 12. Format Data dan Tampilan Nilai

Gunakan format tampilan yang konsisten:
- Mata uang: `Rp 25.000`.
- Tanggal: `19 November 2026` untuk tampilan panjang.
- Tanggal ringkas: `19/11/2026`.
- Waktu: `14.30` atau format 24 jam.
- Desimal stok mengikuti satuan barang.
- Angka nominal rata kanan pada tabel.

Format tampilan tidak boleh mengubah nilai asli yang disimpan di database. Format harus diterapkan secara konsisten pada Blade dan Filament.

## 13. State dan Interaksi Wajib

Setiap halaman perlu mendesain state berikut:

1. **Default:** data tampil normal.
2. **Loading:** pengguna mengetahui proses sedang berjalan.
3. **Empty:** belum ada data, tampilkan penjelasan dan aksi yang relevan.
4. **Error:** jelaskan kesalahan dan cara memperbaiki bila memungkinkan.
5. **Success:** konfirmasi tindakan berhasil.
6. **Disabled:** aksi tidak tersedia terlihat nonaktif.
7. **Validation:** kesalahan form muncul dekat field terkait.
8. **Confirmation:** tindakan penting meminta konfirmasi.

Contoh:
- Kasir tanpa barang di keranjang menampilkan empty state.
- Pencarian barang tanpa hasil menampilkan pesan “Barang tidak ditemukan”.
- Stok gagal diperbarui menampilkan pesan error.
- Serah terima yang sudah dikirim menampilkan status dan tidak menyediakan form edit.
- Dashboard yang belum memiliki transaksi menampilkan empty state, bukan angka atau grafik palsu.

## 14. Aksesibilitas dan Usability

- Gunakan label pada semua input.
- Pastikan teks dan latar memiliki kontras memadai.
- Jangan mengandalkan warna saja untuk menyampaikan status.
- Semua aksi penting dapat diakses dengan keyboard.
- Fokus keyboard harus terlihat.
- Tombol memiliki label yang jelas.
- Area sentuh pada mobile cukup besar, disarankan minimum 44 x 44 px untuk aksi utama.
- Pesan validasi harus dapat dipahami.
- Hindari teks terlalu kecil untuk informasi operasional.

## 15. Pembagian Tanggung Jawab Desain

| Anggota | Area desain | Kewajiban |
|---|---|---|
| RADHITYA | Kasir dan laporan karyawan | Mengikuti seluruh token, komponen, dan layout pada PRD |
| Rafif | Dashboard owner Filament | Menyesuaikan tema Filament agar identik dengan design system |
| Gading | Manajemen stok | Mengikuti token dan komponen yang sama dengan kasir |
| Project Manager | Design system global | Menetapkan token, menyetujui perubahan, dan melakukan review visual |

### 15.1 Aturan kolaborasi

- Jangan membuat warna baru tanpa persetujuan.
- Jangan mengubah font untuk satu halaman saja.
- Jangan membuat ukuran tombol atau input sendiri.
- Jangan membuat komponen yang sama dengan gaya berbeda.
- Jika suatu komponen belum tersedia, usulkan komponen bersama.
- Perubahan token global harus disepakati seluruh anggota.
- Setiap Pull Request UI harus menyertakan screenshot desktop dan mobile.

## 16. Definition of Done UI/UX

Pull Request UI baru dapat diterima jika:

- [ ] Menggunakan font Inter dan fallback yang ditetapkan.
- [ ] Menggunakan palet warna dari design tokens.
- [ ] Ukuran heading dan body sesuai skala tipografi.
- [ ] Tombol mengikuti ukuran dan gaya standar.
- [ ] Input dan select mengikuti standar.
- [ ] Card, tabel, badge, dan modal konsisten.
- [ ] Spacing mengikuti skala 4 px.
- [ ] Radius mengikuti token.
- [ ] State loading, empty, error, dan success tersedia sesuai kebutuhan.
- [ ] Layout diuji pada desktop.
- [ ] Layout diuji pada mobile.
- [ ] Tidak ada overflow yang tidak diperlukan.
- [ ] Screenshot dilampirkan pada Pull Request.
- [ ] Project Manager menyetujui tampilan.

## 17. Checklist Review Visual Project Manager

Saat meninjau Pull Request, periksa:
1. Apakah warna primer sama dengan halaman lain?
2. Apakah font dan ukuran judul sama?
3. Apakah ukuran tombol dan input sama?
4. Apakah radius card dan form sama?
5. Apakah padding halaman konsisten?
6. Apakah tabel dan badge memakai standar yang sama?
7. Apakah format Rupiah dan tanggal konsisten?
8. Apakah tampilan mobile tetap terbaca?
9. Apakah Filament terasa sebagai bagian dari aplikasi yang sama?
10. Apakah screenshot menunjukkan kondisi normal dan state penting?

Jika satu komponen berbeda tanpa alasan yang disetujui, minta perbaikan sebelum merge.

## 18. Keputusan dan Perubahan

PRD ini menjadi acuan desain versi 1.0. Perubahan warna, font, ukuran, radius, atau pola layout harus:
1. Diusulkan melalui GitHub Issue atau diskusi tim.
2. Menjelaskan alasan dan halaman yang terdampak.
3. Disetujui Project Manager.
4. Diterapkan pada token atau komponen bersama.
5. Diperiksa pada halaman Blade dan Filament yang terdampak.

Jangan melakukan perubahan lokal pada satu modul yang menyebabkan tampilan menyimpang dari sistem desain.

---

## Lampiran A. Ringkasan Standar Cepat

| Elemen | Standar |
|---|---|
| Konsep visual | Warm Coffee Minimalist |
| Font | Inter |
| Primary | `#6F4E37` |
| Primary hover | `#805B42` |
| Brand dark | `#3B2A20` |
| Accent | `#D6A354` |
| Page background | `#F7F5F1` |
| Surface | `#FFFFFF` |
| Border | `#E4DED6` |
| Text primary | `#26221E` |
| Text secondary | `#70685F` |
| Page title | 24 px / 700 |
| Card title | 18 px / 600 |
| Body | 14-16 px |
| Button | 40 px tinggi |
| Input | minimum 40 px |
| Card radius | 12 px |
| Input/button radius | 8 px |
| Page padding desktop | 24 px |
| Page padding mobile | 16 px |
| Sidebar Filament | 248 px |
| Topbar | 64 px |
| Max content width | 1440 px |

**Instruksi untuk seluruh developer:** sebelum mulai membangun tampilan, baca PRD ini dan gunakan token serta ukuran yang telah ditentukan. Jika ada kebutuhan yang belum tercakup, tanyakan kepada Project Manager. Jangan membuat standar visual sendiri.
