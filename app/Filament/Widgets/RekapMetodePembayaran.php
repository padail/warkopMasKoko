<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapMetodePembayaran extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected static ?bool $isLazyLoaded = false;

    protected ?string $heading = 'Rekap Metode Pembayaran';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $summary = app(OwnerDashboardService::class)->salesSummary($this->pageFilters);

        return [
            Stat::make('Tunai', OwnerDashboardService::formatRupiah($summary['revenue_tunai']))
                ->description($summary['count_tunai'].' transaksi tunai')
                ->descriptionIcon(Heroicon::Banknotes)
                ->icon(Heroicon::Wallet)
                ->color('success'),
            Stat::make('QRIS', OwnerDashboardService::formatRupiah($summary['revenue_qris']))
                ->description($summary['count_qris'].' transaksi QRIS')
                ->descriptionIcon(Heroicon::QrCode)
                ->icon(Heroicon::QrCode)
                ->color('info'),
            Stat::make('GoPay', OwnerDashboardService::formatRupiah($summary['revenue_gopay']))
                ->description($summary['count_gopay'].' transaksi GoPay')
                ->descriptionIcon(Heroicon::DevicePhoneMobile)
                ->icon(Heroicon::DevicePhoneMobile)
                ->color('warning'),
        ];
    }
}
