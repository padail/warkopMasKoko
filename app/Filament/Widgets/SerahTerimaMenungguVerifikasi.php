<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SerahTerimaMenungguVerifikasi extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 7;

    protected static ?bool $isLazyLoaded = false;

    protected ?string $heading = 'Serah Terima Kas';

    protected ?string $description = 'Rekap laporan kas karyawan berdasarkan filter tanggal aktif.';

    protected int|array|null $columns = 5;

    protected function getStats(): array
    {
        $summary = app(OwnerDashboardService::class)->handoverSummary($this->pageFilters);

        return [
            Stat::make('Menunggu Verifikasi', (string) $summary['pending_count'])
                ->description('Belum diperiksa owner')
                ->descriptionIcon(Heroicon::Clock)
                ->icon(Heroicon::InboxArrowDown)
                ->color($summary['pending_count'] > 0 ? 'warning' : 'gray'),
            Stat::make('Sesuai', (string) $summary['sesuai_count'])
                ->description('Selisih nol')
                ->icon(Heroicon::ClipboardDocumentCheck)
                ->color('success'),
            Stat::make('Lebih', (string) $summary['lebih_count'])
                ->description('Kas diserahkan melebihi harapan')
                ->icon(Heroicon::ArrowTrendingUp)
                ->color('info'),
            Stat::make('Kurang', (string) $summary['kurang_count'])
                ->description('Kas diserahkan kurang')
                ->icon(Heroicon::ExclamationTriangle)
                ->color('danger'),
            Stat::make('Total Selisih', OwnerDashboardService::formatRupiah($summary['difference_total']))
                ->description('Aktual - harus disetor')
                ->icon(Heroicon::Scale)
                ->color($summary['difference_total'] < 0 ? 'danger' : 'gray'),
        ];
    }
}
