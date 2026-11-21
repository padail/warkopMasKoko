<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JumlahTransaksi extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected static ?bool $isLazyLoaded = false;

    protected ?string $heading = 'Jumlah Transaksi';

    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        $service = app(OwnerDashboardService::class);
        $summary = $service->salesSummary($this->pageFilters);

        return [
            Stat::make('Transaksi Selesai', number_format($summary['transactions_count'], 0, ',', '.'))
                ->description($service->resolveRange($this->pageFilters)['label'])
                ->descriptionIcon(Heroicon::CalendarDays)
                ->icon(Heroicon::ShoppingCart)
                ->color('primary'),
            Stat::make('Rata-rata per Transaksi', OwnerDashboardService::formatRupiah($summary['average_per_transaction']))
                ->description('Dari transaksi berstatus selesai')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->icon(Heroicon::Scale)
                ->color('gray'),
        ];
    }
}
