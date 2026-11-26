<?php

namespace App\Services\AI;

use App\Models\DetailTransaksi;
use App\Models\Pengeluaran;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Illuminate\Support\Carbon;

class BusinessInsightService
{
    public function __construct(
        protected OwnerDashboardService $dashboard
    ) {}

    /**
     * Menyusun konteks bisnis yang sudah dihitung Laravel untuk dikirim ke AI.
     *
     * Semua angka berasal dari database. Service ini tidak memanggil Groq.
     *
     * @param  array<string, mixed>|null  $filters
     * @return array<string, mixed>
     */
    public function buildContext(?array $filters = null): array
    {
        $range = $this->dashboard->resolveRange($filters);
        $sales = $this->dashboard->salesSummary($filters);

        $previousRange = $this->previousRange($range);
        $previousFilters = [
            'periode' => OwnerDashboardService::PRESET_RENTANG,
            'start' => $previousRange['start']->toDateString(),
            'end' => $previousRange['end']->toDateString(),
        ];
        $previousSales = $this->dashboard->salesSummary($previousFilters);

        $stockSummary = $this->dashboard->stockSummary();

        return [
            'period' => [
                'start' => $range['start']->toDateString(),
                'end' => $range['end']->toDateString(),
                'label' => $range['label'],
                'days' => $this->periodDays($range),
            ],
            'sales' => $this->formatSales($sales),
            'sales_comparison' => $this->formatComparison(
                $sales,
                $previousSales,
                $previousRange
            ),
            'top_products' => $this->topProducts($range['start'], $range['end']),
            'stock' => [
                'as_of' => now()->toIso8601String(),
                'summary' => [
                    'active_count' => $stockSummary['active_count'],
                    // sold_count pada OwnerDashboardService sebenarnya menghitung
                    // barang yang ditandai is_dijual, bukan jumlah unit terjual.
                    'for_sale_count' => $stockSummary['sold_count'],
                    'out_count' => $stockSummary['out_count'],
                    'low_count' => $stockSummary['low_count'],
                    'safe_count' => $stockSummary['safe_count'],
                ],
                'definitions' => [
                    'active_count' => 'Jumlah barang aktif di master persediaan.',
                    'for_sale_count' => 'Jumlah barang yang ditandai untuk dijual, bukan jumlah barang yang sudah terjual.',
                    'out_count' => 'Jumlah barang aktif dengan stok nol atau kurang.',
                    'low_count' => 'Jumlah barang aktif dengan stok di atas nol tetapi sama dengan atau di bawah batas minimum.',
                    'safe_count' => 'Jumlah barang aktif dengan stok di atas batas minimum.',
                ],
                'alerts' => $this->dashboard->lowStockItems()
                    ->map(fn ($item): array => [
                        'name' => (string) $item->nama,
                        'category' => (string) $item->kategori,
                        'current_stock' => (float) $item->stok,
                        'unit' => (string) $item->satuan,
                        'minimum_stock' => (float) $item->stok_minimum,
                        'status' => $item->status_stok,
                        'is_for_sale' => (bool) $item->is_dijual,
                    ])
                    ->values()
                    ->all(),
            ],
            'expenses' => $this->expenseSummary(
                $range['start'],
                $range['end']
            ),
            'data_limitations' => [
                'Omzet dihitung dari transaksi berstatus selesai dan bukan laba bersih.',
                'Pengeluaran hanya mencakup catatan pada tabel pengeluaran dalam periode yang dipilih.',
                'Selisih omzet dan pengeluaran tercatat bukan laba bersih karena harga pokok penjualan dan seluruh biaya usaha belum tentu tercatat.',
                'Data stok menunjukkan kondisi stok saat ini, bukan rekonstruksi stok pada akhir periode historis.',
                'Produk terlaris diurutkan berdasarkan jumlah unit terjual, bukan margin keuntungan.',
            ],
        ];
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     * @return array{start: Carbon, end: Carbon}
     */
    protected function previousRange(array $range): array
    {
        $days = $this->periodDays($range);
        $end = $range['start']->copy()->subSecond();
        $start = $end->copy()->startOfDay()->subDays($days - 1);

        return [
            'start' => $start->startOfDay(),
            'end' => $end->endOfDay(),
        ];
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $range
     */
    protected function periodDays(array $range): int
    {
        return (int) $range['start']->copy()
            ->startOfDay()
            ->diffInDays($range['end']->copy()->startOfDay()) + 1;
    }

    /**
     * @param  array<string, int>  $sales
     * @return array<string, mixed>
     */
    protected function formatSales(array $sales): array
    {
        return [
            'transactions_count' => $sales['transactions_count'],
            'revenue_total' => $sales['revenue_total'],
            'average_per_transaction' => $sales['average_per_transaction'],
            'payment_methods' => [
                'tunai' => [
                    'transactions_count' => $sales['count_tunai'],
                    'revenue_total' => $sales['revenue_tunai'],
                ],
                'qris' => [
                    'transactions_count' => $sales['count_qris'],
                    'revenue_total' => $sales['revenue_qris'],
                ],
                'gopay' => [
                    'transactions_count' => $sales['count_gopay'],
                    'revenue_total' => $sales['revenue_gopay'],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, int>  $current
     * @param  array<string, int>  $previous
     * @param  array{start: Carbon, end: Carbon}  $previousRange
     * @return array<string, mixed>
     */
    protected function formatComparison(
        array $current,
        array $previous,
        array $previousRange
    ): array {
        $revenueDifference = $current['revenue_total'] - $previous['revenue_total'];
        $previousRevenue = $previous['revenue_total'];

        $percentageChange = $previousRevenue > 0
            ? round(($revenueDifference / $previousRevenue) * 100, 2)
            : null;

        $direction = match (true) {
            $previousRevenue === 0 && $current['revenue_total'] > 0 => 'aktivitas_baru',
            $previousRevenue === 0 => 'belum_ada_baseline',
            $revenueDifference > 0 => 'naik',
            $revenueDifference < 0 => 'turun',
            default => 'stabil',
        };

        return [
            'previous_period' => [
                'start' => $previousRange['start']->toDateString(),
                'end' => $previousRange['end']->toDateString(),
            ],
            'previous_revenue_total' => $previousRevenue,
            'revenue_difference' => $revenueDifference,
            'revenue_change_percent' => $percentageChange,
            'revenue_direction' => $direction,
            'previous_transactions_count' => $previous['transactions_count'],
            'transactions_difference' => $current['transactions_count']
                - $previous['transactions_count'],
        ];
    }

    /**
     * @return array<int, array{
     *     product_name: string,
     *     quantity_sold: float,
     *     revenue_total: int,
     *     transactions_count: int
     * }>
     */
    protected function topProducts(Carbon $start, Carbon $end): array
    {
        return DetailTransaksi::query()
            ->join('transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.status', 'selesai')
            ->whereBetween('transaksi.tanggal_transaksi', [$start, $end])
            ->selectRaw('
                detail_transaksi.nama_item_snapshot as product_name,
                COALESCE(SUM(detail_transaksi.jumlah), 0) as quantity_sold,
                COALESCE(SUM(detail_transaksi.subtotal), 0) as revenue_total,
                COUNT(DISTINCT transaksi.id) as transactions_count
            ')
            ->groupBy('detail_transaksi.nama_item_snapshot')
            ->orderByDesc('quantity_sold')
            ->orderByDesc('revenue_total')
            ->limit(5)
            ->get()
            ->map(fn ($row): array => [
                'product_name' => (string) $row->product_name,
                'quantity_sold' => round((float) $row->quantity_sold, 3),
                'revenue_total' => (int) $row->revenue_total,
                'transactions_count' => (int) $row->transactions_count,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function expenseSummary(Carbon $start, Carbon $end): array
    {
        // Kolom tanggal_pengeluaran bertipe DATE. Gunakan whereDate()
        // agar konsisten pada SQLite dan MySQL, termasuk ketika SQLite
        // menyimpan representasi tanggal sebagai datetime.
        $expenses = Pengeluaran::query()
            ->whereDate('tanggal_pengeluaran', '>=', $start->toDateString())
            ->whereDate('tanggal_pengeluaran', '<=', $end->toDateString());

        $totals = (clone $expenses)
            ->selectRaw('COUNT(*) as expenses_count, COALESCE(SUM(jumlah), 0) as expenses_total')
            ->first();

        $byCategory = (clone $expenses)
            ->selectRaw('kategori, COUNT(*) as expenses_count, COALESCE(SUM(jumlah), 0) as expenses_total')
            ->groupBy('kategori')
            ->orderByDesc('expenses_total')
            ->get()
            ->map(fn ($row): array => [
                'category' => (string) $row->kategori,
                'expenses_count' => (int) $row->expenses_count,
                'expenses_total' => (int) $row->expenses_total,
            ])
            ->values()
            ->all();

        return [
            'expenses_count' => (int) ($totals->expenses_count ?? 0),
            'expenses_total' => (int) ($totals->expenses_total ?? 0),
            'by_category' => $byCategory,
        ];
    }
}
