<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanStok extends StatsOverviewWidget
{
    protected static ?int $sort = 8;

    protected static ?bool $isLazyLoaded = false;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $summary = app(OwnerDashboardService::class)->stockSummary();

        return [
            Stat::make('Barang Aktif', $summary['active_count'])
                ->description('Termasuk barang dijual dan tidak dijual')
                ->descriptionIcon(Heroicon::OutlinedArchiveBox)
                ->color('info'),
            Stat::make('Stok Habis', $summary['out_count'])
                ->description('Stok 0 atau kurang')
                ->descriptionIcon(Heroicon::OutlinedArchiveBoxXMark)
                ->color('danger'),
            Stat::make('Stok Menipis', $summary['low_count'])
                ->description('Di atas 0 dan mencapai batas minimum')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('warning'),
            Stat::make('Stok Aman', $summary['safe_count'])
                ->description('Di atas batas minimum')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
        ];
    }
}
