<?php

namespace App\Services;

use App\Models\MenuBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StokService
{
    /**
     * Daftar alasan baku pembaruan stok sesuai PEMBAGIAN_TUGAS.md.
     *
     * @return list<string>
     */
    public static function alasanOptions(): array
    {
        return [
            'Barang masuk',
            'Hasil pemeriksaan stok',
            'Koreksi pencatatan',
            'Barang rusak atau hilang',
            'Lainnya',
        ];
    }

    /**
     * @param  array{search?: ?string, kategori?: ?string, status_stok?: ?string}  $filters
     */
    public function paginateItems(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = MenuBarang::query()->with('stokDiperbaruiOleh');

        if (filled($filters['search'] ?? null)) {
            $search = trim((string) $filters['search']);
            $query->where('nama', 'like', "%{$search}%");
        }

        if (filled($filters['kategori'] ?? null) && $filters['kategori'] !== 'semua') {
            $query->where('kategori', $filters['kategori']);
        }

        $statusStok = $filters['status_stok'] ?? null;
        if ($statusStok === MenuBarang::STATUS_STOK_HABIS) {
            $query->where('stok', '<=', 0);
        } elseif ($statusStok === MenuBarang::STATUS_STOK_MENIPIS) {
            $query->where('stok', '>', 0)
                ->whereColumn('stok', '<=', 'stok_minimum');
        } elseif ($statusStok === MenuBarang::STATUS_STOK_NORMAL) {
            $query->where('stok', '>', 0)
                ->whereColumn('stok', '>', 'stok_minimum');
        }

        return $query
            ->orderByDesc('is_active')
            ->orderByRaw('CASE WHEN stok <= 0 THEN 0 WHEN stok <= stok_minimum THEN 1 ELSE 2 END')
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array{total: int, normal: int, menipis: int, habis: int}
     */
    public function summaryCounts(): array
    {
        $activeQuery = MenuBarang::query()->where('is_active', true);

        $total = (clone $activeQuery)->count();
        $habis = (clone $activeQuery)->where('stok', '<=', 0)->count();
        $menipis = (clone $activeQuery)
            ->where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();

        return [
            'total' => $total,
            'normal' => max(0, $total - $habis - $menipis),
            'menipis' => $menipis,
            'habis' => $habis,
        ];
    }

    /**
     * @return Collection<int, string>
     */
    public function categories(): Collection
    {
        $defaults = collect(['Makanan', 'Minuman', 'Snack', 'Bahan Baku']);
        $existing = MenuBarang::query()
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return $defaults->merge($existing)->unique()->values();
    }

    /**
     * Memperbarui jumlah stok secara atomik dengan row locking.
     */
    public function updateStock(
        MenuBarang $menuBarang,
        int $userId,
        string $mode,
        float $nilai,
        string $alasan
    ): MenuBarang {
        return DB::transaction(function () use ($menuBarang, $userId, $mode, $nilai, $alasan): MenuBarang {
            $lockedItem = MenuBarang::query()
                ->whereKey($menuBarang->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $stokSekarang = (float) $lockedItem->stok;
            $stokBaru = match ($mode) {
                'tambah' => $stokSekarang + $nilai,
                'kurang' => $stokSekarang - $nilai,
                default => $nilai,
            };

            if ($stokBaru < 0) {
                throw new RuntimeException('Stok hasil pembaruan tidak boleh bernilai negatif.');
            }

            $lockedItem->update([
                'stok' => round($stokBaru, 3),
                'stok_diperbarui_oleh' => $userId,
                'alasan_perubahan_stok' => $alasan,
                'stok_diperbarui_pada' => now(),
            ]);

            return $lockedItem->fresh(['stokDiperbaruiOleh']);
        });
    }

    /**
     * Membuat barang baru (khusus admin).
     *
     * @param  array<string, mixed>  $data
     */
    public function createItem(array $data, int $userId): MenuBarang
    {
        return MenuBarang::create([
            'nama' => $data['nama'],
            'kategori' => $data['kategori'],
            'harga_jual' => (int) ($data['harga_jual'] ?? 0),
            'stok' => round((float) $data['stok'], 3),
            'satuan' => $data['satuan'],
            'stok_minimum' => round((float) $data['stok_minimum'], 3),
            'is_dijual' => (bool) ($data['is_dijual'] ?? true),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'stok_diperbarui_oleh' => $userId,
            'alasan_perubahan_stok' => 'Barang masuk',
            'stok_diperbarui_pada' => now(),
        ]);
    }

    /**
     * Memperbarui identitas, harga, satuan, dan status barang (khusus admin).
     *
     * @param  array<string, mixed>  $data
     */
    public function updateItemDetails(MenuBarang $menuBarang, array $data, int $userId): MenuBarang
    {
        return DB::transaction(function () use ($menuBarang, $data, $userId): MenuBarang {
            $lockedItem = MenuBarang::query()
                ->whereKey($menuBarang->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $payload = [
                'nama' => $data['nama'],
                'kategori' => $data['kategori'],
                'harga_jual' => (int) ($data['harga_jual'] ?? 0),
                'satuan' => $data['satuan'],
                'stok_minimum' => round((float) $data['stok_minimum'], 3),
                'is_dijual' => (bool) ($data['is_dijual'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? false),
            ];

            if (array_key_exists('stok', $data) && $data['stok'] !== null && (float) $data['stok'] !== (float) $lockedItem->stok) {
                $payload['stok'] = round((float) $data['stok'], 3);
                $payload['stok_diperbarui_oleh'] = $userId;
                $payload['alasan_perubahan_stok'] = $data['alasan'] ?? 'Koreksi pencatatan';
                $payload['stok_diperbarui_pada'] = now();
            }

            $lockedItem->update($payload);

            return $lockedItem->fresh(['stokDiperbaruiOleh']);
        });
    }

    /**
     * Menonaktifkan/mengaktifkan barang tanpa menghapus riwayat transaksi.
     */
    public function toggleActive(MenuBarang $menuBarang): MenuBarang
    {
        $menuBarang->update([
            'is_active' => ! $menuBarang->is_active,
        ]);

        return $menuBarang;
    }
}
