<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendapatanHariIni extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static ?bool $isLazyLoaded = false;

    protected ?string $heading = 'Total Pendapatan';

    protected int|array|null $columns = 1;

    protected function getStats(): array
    {
        $service = app(OwnerDashboardService::class);
        $summary = $service->salesSummary($this->pageFilters);

        return [
            Stat::make('Pendapatan', OwnerDashboardService::formatRupiah($summary['revenue_total']))
                ->description($service->resolveRange($this->pageFilters)['label'])
                ->descriptionIcon(Heroicon::CalendarDays)
                ->icon(Heroicon::Banknotes)
                ->color('success'),
        ];
    }
}
