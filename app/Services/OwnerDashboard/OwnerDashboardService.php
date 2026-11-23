<?php

namespace App\Services\OwnerDashboard;

use App\Models\MenuBarang;
use App\Models\SerahTerimaKas;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OwnerDashboardService
{
    public const PRESET_HARI_INI = 'hari_ini';

    public const PRESET_KEMARIN = 'kemarin';

    public const PRESET_RENTANG = 'rentang';

    public const METODE_TUNAI = 'tunai';

    public const METODE_QRIS = 'qris';

    public const METODE_GOPAY = 'gopay';

    /**
     * Resolve the filter payload into a concrete date range.
     *
     * @return array{start: Carbon, end: Carbon, preset: string, label: string}
     */
    public function resolveRange(?array $filters = null): array
    {
        $filters ??= [];

        $preset = $filters['periode'] ?? self::PRESET_HARI_INI;
        $today = Carbon::today();

        [$start, $end] = match ($preset) {
            self::PRESET_KEMARIN => [$today->copy()->subDay(), $today->copy()->subDay()],
            self::PRESET_RENTANG => [
                $this->parseDate($filters['start'] ?? null) ?? $today->copy(),
                $this->parseDate($filters['end'] ?? null) ?? $today->copy(),
            ],
            default => [$today->copy(), $today->copy()],
        };

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end, $start];
        }

        return [
            'start' => $start->copy()->startOfDay(),
            'end' => $end->copy()->endOfDay(),
            'preset' => $preset,
            'label' => $start->isSameDay($end)
                ? $start->translatedFormat('d F Y')
                : $start->translatedFormat('d F Y').' - '.$end->translatedFormat('d F Y'),
        ];
    }

    /**
     * @return array{
     *     transactions_count: int,
     *     revenue_total: int,
     *     revenue_tunai: int,
     *     revenue_qris: int,
     *     revenue_gopay: int,
     *     count_tunai: int,
     *     count_qris: int,
     *     count_gopay: int,
     *     average_per_transaction: int
     * }
     */
    public function salesSummary(?array $filters = null): array
    {
        $range = $this->resolveRange($filters);

        $perMethod = $this->completedTransactions($range)
            ->selectRaw('metode_pembayaran, count(*) as jumlah, coalesce(sum(total), 0) as omzet')
            ->groupBy('metode_pembayaran')
            ->get()
            ->keyBy('metode_pembayaran');

        $methodRevenue = fn (string $method): int => (int) ($perMethod->get($method)->omzet ?? 0);
        $methodCount = fn (string $method): int => (int) ($perMethod->get($method)->jumlah ?? 0);

        $revenueTunai = $methodRevenue(self::METODE_TUNAI);
        $revenueQris = $methodRevenue(self::METODE_QRIS);
        $revenueGopay = $methodRevenue(self::METODE_GOPAY);
        $revenueTotal = $revenueTunai + $revenueQris + $revenueGopay;
        $transactionsCount = $methodCount(self::METODE_TUNAI) + $methodCount(self::METODE_QRIS) + $methodCount(self::METODE_GOPAY);

        return [
            'transactions_count' => $transactionsCount,
            'revenue_total' => $revenueTotal,
            'revenue_tunai' => $revenueTunai,
            'revenue_qris' => $revenueQris,
            'revenue_gopay' => $revenueGopay,
            'count_tunai' => $methodCount(self::METODE_TUNAI),
            'count_qris' => $methodCount(self::METODE_QRIS),
            'count_gopay' => $methodCount(self::METODE_GOPAY),
            'average_per_transaction' => $transactionsCount > 0
                ? intdiv($revenueTotal, $transactionsCount)
                : 0,
        ];
    }

    /**
     * @return Collection<int, array{user_id: int, name: string, transactions_count: int, revenue_total: int}>
     */
    public function salesByEmployee(?array $filters = null): Collection
    {
        $range = $this->resolveRange($filters);

        return $this->completedTransactions($range)
            ->leftJoin('users', 'users.id', '=', 'transaksi.user_id')
            ->selectRaw('transaksi.user_id as user_id, users.name as name, count(*) as transactions_count, coalesce(sum(transaksi.total), 0) as revenue_total')
            ->groupBy('transaksi.user_id', 'users.name')
            ->orderByDesc('revenue_total')
            ->get()
            ->map(fn (object $row): array => [
                'user_id' => (int) $row->user_id,
                'name' => (string) ($row->name ?? 'Tidak diketahui'),
                'transactions_count' => (int) $row->transactions_count,
                'revenue_total' => (int) $row->revenue_total,
            ]);
    }

    /**
     * @return Builder<MenuBarang>
     */
    public function lowStockQuery(): Builder
    {
        return MenuBarang::query()
            ->where('is_active', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok');
    }

    /**
     * @return Collection<int, MenuBarang>
     */
    public function lowStockItems(): Collection
    {
        return $this->lowStockQuery()->get();
    }

    /**
     * @return array{
     *     active_count: int,
     *     sold_count: int,
     *     out_count: int,
     *     low_count: int,
     *     safe_count: int
     * }
     */
    public function stockSummary(): array
    {
        $activeItems = MenuBarang::query()->where('is_active', true);

        $outCount = (clone $activeItems)->where('stok', '<=', 0)->count();
        $lowCount = (clone $activeItems)
            ->where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();
        $safeCount = (clone $activeItems)
            ->whereColumn('stok', '>', 'stok_minimum')
            ->count();

        return [
            'active_count' => (clone $activeItems)->count(),
            'sold_count' => (clone $activeItems)->where('is_dijual', true)->count(),
            'out_count' => $outCount,
            'low_count' => $lowCount,
            'safe_count' => $safeCount,
        ];
    }

    /**
     * @return Builder<Transaksi>
     */
    public function latestTransactions(?array $filters = null): Builder
    {
        $range = $this->resolveRange($filters);

        return $this->completedTransactions($range)
            ->with('user')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id');
    }

    /**
     * @return Builder<SerahTerimaKas>
     */
    public function handovers(?array $filters = null): Builder
    {
        $range = $this->resolveRange($filters);

        return SerahTerimaKas::query()
            ->with(['user', 'diterimaOleh'])
            ->whereBetween('tanggal_serah', [$range['start']->toDateString(), $range['end']->toDateString()])
            ->orderByDesc('tanggal_serah')
            ->orderByDesc('id');
    }

    /**
     * @return array{
     *     pending_count: int,
     *     verified_count: int,
     *     sesuai_count: int,
     *     lebih_count: int,
     *     kurang_count: int,
     *     expected_total: int,
     *     actual_total: int,
     *     difference_total: int
     * }
     */
    public function handoverSummary(?array $filters = null): array
    {
        $handovers = $this->handovers($filters)->get();

        return [
            'pending_count' => $handovers->filter(fn (SerahTerimaKas $handover): bool => $handover->isMenunggu())->count(),
            'verified_count' => $handovers->filter(fn (SerahTerimaKas $handover): bool => $handover->isTerverifikasi())->count(),
            'sesuai_count' => $handovers->filter(fn (SerahTerimaKas $handover): bool => $handover->selisih_klasifikasi === SerahTerimaKas::SELISIH_SESUAI)->count(),
            'lebih_count' => $handovers->filter(fn (SerahTerimaKas $handover): bool => $handover->selisih_klasifikasi === SerahTerimaKas::SELISIH_LEBIH)->count(),
            'kurang_count' => $handovers->filter(fn (SerahTerimaKas $handover): bool => $handover->selisih_klasifikasi === SerahTerimaKas::SELISIH_KURANG)->count(),
            'expected_total' => (int) $handovers->sum('uang_harus_disetor'),
            'actual_total' => (int) $handovers->sum('uang_aktual'),
            'difference_total' => (int) $handovers->sum('selisih'),
        ];
    }

    /**
     * Count the cash transactions an employee completed on the handover date.
     */
    public function tunaiTransactionCount(SerahTerimaKas $handover): int
    {
        return Transaksi::query()
            ->where('user_id', $handover->user_id)
            ->where('metode_pembayaran', self::METODE_TUNAI)
            ->where('status', 'selesai')
            ->whereDate('tanggal_transaksi', $handover->tanggal_serah)
            ->count();
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     * @return Builder<Transaksi>
     */
    protected function completedTransactions(array $range): Builder
    {
        return Transaksi::query()
            ->where('status', 'selesai')
            ->where('tanggal_transaksi', '>=', $range['start'])
            ->where('tanggal_transaksi', '<=', $range['end']);
    }

    protected function parseDate(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, string>
     */
    public static function periodOptions(): array
    {
        return [
            self::PRESET_HARI_INI => 'Hari ini',
            self::PRESET_KEMARIN => 'Kemarin',
            self::PRESET_RENTANG => 'Rentang tanggal',
        ];
    }

    public static function formatRupiah(int|float|string|null $amount): string
    {
        return 'Rp '.number_format((int) $amount, 0, ',', '.');
    }

    /**
     * @return Collection<int, User>
     */
    public function adminUsers(): Collection
    {
        return User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, string>
     */
    public static function methodOptions(): array
    {
        return [
            self::METODE_TUNAI => 'Tunai',
            self::METODE_QRIS => 'QRIS',
            self::METODE_GOPAY => 'GoPay',
        ];
    }
}
