# Perbaikan Sidebar dan Redirect Logout Filament

Patch ini menargetkan Filament `v5.11.4` yang tercantum pada `composer.lock`.

## Perbaikan sidebar

Theme sebelumnya memakai `.fi-sidebar-item-btn`, sementara hook menu Filament yang relevan adalah `.fi-sidebar-item-button` dan `.fi-sidebar-item-label`. Theme sekarang secara eksplisit menargetkan label, ikon, judul grup, serta ikon tombol sidebar. Warna teks menu dipaksa menjadi putih agar tidak dikalahkan utility text color bawaan.

## Perbaikan logout

Filament memakai contract `Filament\Auth\Http\Responses\Contracts\LogoutResponse`. Implementasi aplikasi sekarang mengarahkan hasil logout ke named route `login`, yang pada proyek ini adalah `/login`.

Tidak perlu membuat route login baru dan tidak perlu mengubah route login yang sudah ada.

## File

- `resources/css/filament/admin/theme.css`
- `app/Http/Responses/FilamentLogoutResponse.php`
- `app/Providers/AppServiceProvider.php`
- `tests/Feature/FilamentLogoutRedirectTest.php`

## Terapkan

Ekstrak ZIP ke root Laravel project dan izinkan overwrite file yang sama.

Lalu jalankan:

```bash
npm run build
php artisan optimize:clear
php artisan test --filter=FilamentLogoutRedirectTest
```

Setelah build, lakukan hard refresh browser (`Ctrl + Shift + R`) pada `/admin`.
