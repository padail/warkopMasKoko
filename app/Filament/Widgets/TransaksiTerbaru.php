<?php

namespace App\Filament\Widgets;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TransaksiTerbaru extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;

    protected static ?bool $isLazyLoaded = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Transaksi Terbaru')
            ->description('Transaksi berstatus selesai pada periode terpilih.')
            ->query(fn (): Builder => app(OwnerDashboardService::class)->latestTransactions($this->pageFilters))
            ->columns([
                TextColumn::make('kode_transaksi')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->placeholder('-'),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => OwnerDashboardService::methodOptions()[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        OwnerDashboardService::METODE_TUNAI => 'success',
                        OwnerDashboardService::METODE_QRIS => 'info',
                        OwnerDashboardService::METODE_GOPAY => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->defaultSort('tanggal_transaksi', 'desc')
            ->emptyStateHeading('Belum ada transaksi')
            ->emptyStateDescription('Tidak ada transaksi selesai pada periode ini.')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->striped();
    }
}
