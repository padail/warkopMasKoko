# Filament Theme Alignment

Patch ini menyelaraskan panel admin Filament dengan design tokens Tailwind Warkop Mas Koko.

## File

- `resources/css/design-tokens.css`: sumber bersama token warna.
- `resources/css/app.css`: mengimpor token untuk halaman Tailwind.
- `resources/css/filament/admin/theme.css`: mengimpor token yang sama dan menata CSS hooks Filament.
- `app/Providers/Filament/AdminPanelProvider.php`: memakai palet gray bernuansa hangat dan menonaktifkan dark mode agar panel konsisten dengan desain terang.

## Palet

Primary `#6F4E37`, hover `#805B42`, sidebar `#3B2A20`, page `#F7F5F1`, surface `#FFFFFF`, muted surface `#F1EEE8`, border `#E4DED6`, text primary `#26221E`, text secondary `#70685F`, accent `#D6A354`. Status success, warning, danger, dan info memakai token proyek.

## Cara menerapkan

Ekstrak ZIP ke root repository Laravel dan izinkan overwrite file yang sama.

Jalankan:

```bash
npm run build
php artisan optimize:clear
```

Kemudian periksa `/admin`: dashboard, tabel resource, form, badge status, notifikasi, dan sidebar pada desktop maupun mobile.

Patch ini hanya mengubah tampilan panel. Resource, alur bisnis, database, route, dan permission tidak diubah.
