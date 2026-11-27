# Penyempurnaan UI Mobile — Area Karyawan

## Cakupan

Patch ini mengoptimalkan tampilan untuk penggunaan ponsel pada:

- Kasir
- Manajemen stok
- Laporan keuangan
- Form pembaruan stok
- Halaman login

## Perubahan UI

### Login
- Form login memakai lebar penuh pada ponsel, input tinggi 48 px, dan ukuran teks yang mencegah zoom otomatis pada Safari iOS.

### Navigasi
- Navigasi desktop tetap horizontal.
- Karyawan mendapatkan bottom navigation pada layar di bawah breakpoint `md`.
- Bottom navigation menyediakan Kasir, Stok, dan Laporan.
- Konten diberi ruang bawah agar tidak tertutup navigasi.
- Viewport menggunakan `viewport-fit=cover` untuk perangkat dengan safe area.

### Kasir
- Search dan kategori dibuat nyaman untuk layar sentuh.
- Kartu produk memakai satu kolom di layar sempit.
- Tombol tambah, ubah jumlah, dan hapus memiliki target sentuh lebih besar.
- Keranjang menampilkan empty state.
- Shortcut keranjang tetap terlihat di atas bottom navigation ketika keranjang berisi barang dan panel keranjang belum terlihat.
- Dialog pembayaran menyesuaikan tinggi viewport.
- Rendering teks keranjang dan struk menggunakan `textContent`, bukan menyisipkan nama produk ke `innerHTML`.

### Laporan keuangan
- Ringkasan angka menggunakan dua kolom di layar ponsel.
- Form tanggal dan input pengeluaran disusun satu kolom.
- Riwayat pemasukan dan pengeluaran berubah menjadi kartu di mobile; tabel tetap digunakan pada desktop.
- Setoran harian menggunakan kartu ringkasan dan tombol lebar penuh pada mobile.

### Stok
- KPI dan filter disusun ulang agar ringkas.
- Daftar stok memakai kartu pada mobile dan tabel pada desktop.
- Modal tambah barang dapat discroll pada layar pendek.
- Form stok memakai input dan tombol dengan area sentuh yang lebih besar.

## Batas perubahan

Patch ini hanya mengubah tampilan. Route, controller, perhitungan, validasi, dan izin backend tidak diubah.

## Cara menerapkan

Ekstrak ZIP ke root repository Laravel dan izinkan overwrite.

Kemudian jalankan:

```bash
npm run build
php artisan optimize:clear
php artisan test --filter=EmployeeMobileLayoutTest
```

## Checklist pengujian manual

Uji minimal pada lebar 360 px dan 390 px:

1. Navigasi bawah tidak menutupi tombol atau konten.
2. Kasir dapat menambah barang dan membuka keranjang dari shortcut bawah.
3. Dialog konfirmasi dan struk muat di tinggi layar.
4. Form pengeluaran dapat diisi tanpa zoom otomatis.
5. Riwayat pemasukan/pengeluaran tampil sebagai kartu.
6. Kartu stok tidak menyebabkan scroll horizontal seluruh halaman.
7. Modal tambah barang tetap dapat discroll.
8. Tombol Keluar tetap dapat dijangkau.
