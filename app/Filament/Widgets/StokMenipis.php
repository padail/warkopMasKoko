<?php

namespace App\Filament\Widgets;

use App\Models\MenuBarang;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class StokMenipis extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected static ?bool $isLazyLoaded = false;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Stok Menipis')
            ->description('Barang aktif yang stoknya sudah mencapai atau di bawah batas minimum.')
            ->query(fn (): Builder => app(OwnerDashboardService::class)->lowStockQuery())
            ->columns([
                TextColumn::make('nama')
                    ->label('Barang')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric(decimalPlaces: 0)
                    ->badge()
                    ->color(fn (MenuBarang $record): string => (float) $record->stok <= 0 ? 'danger' : 'warning')
                    ->alignEnd(),
                TextColumn::make('stok_minimum')
                    ->label('Minimum')
                    ->numeric(decimalPlaces: 0)
                    ->alignEnd(),
                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->color('gray'),
            ])
            ->emptyStateHeading('Semua stok aman')
            ->emptyStateDescription('Tidak ada barang yang berada di bawah batas minimum.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->striped();
    }
}
