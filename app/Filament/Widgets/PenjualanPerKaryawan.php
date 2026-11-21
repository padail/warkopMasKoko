<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class PenjualanPerKaryawan extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected static ?bool $isLazyLoaded = false;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Penjualan per Karyawan')
            ->description('Jumlah transaksi selesai dan total penjualan setiap karyawan.')
            ->records(fn (): Collection => app(OwnerDashboardService::class)->salesByEmployee($this->pageFilters))
            ->columns([
                TextColumn::make('name')
                    ->label('Karyawan')
                    ->weight('medium'),
                TextColumn::make('transactions_count')
                    ->label('Transaksi')
                    ->badge()
                    ->color('gray')
                    ->alignEnd(),
                TextColumn::make('revenue_total')
                    ->label('Total Penjualan')
                    ->money('IDR', locale: 'id')
                    ->alignEnd(),
            ])
            ->emptyStateHeading('Belum ada penjualan')
            ->emptyStateDescription('Tidak ada transaksi selesai pada periode ini.')
            ->striped();
    }
}
