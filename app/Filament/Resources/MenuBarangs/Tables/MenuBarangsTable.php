<?php

namespace App\Filament\Resources\MenuBarangs\Tables;

use App\Models\MenuBarang;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama barang')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric(decimalPlaces: 3)
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('stok_minimum')
                    ->label('Minimum')
                    ->numeric(decimalPlaces: 3)
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('status_stok')
                    ->label('Status')
                    ->state(fn (MenuBarang $record): string => match (true) {
                        (float) $record->stok <= 0 => 'Habis',
                        (float) $record->stok <= (float) $record->stok_minimum => 'Menipis',
                        default => 'Aman',
                    })
                    ->badge()
                    ->color(fn (MenuBarang $record): string => match (true) {
                        (float) $record->stok <= 0 => 'danger',
                        (float) $record->stok <= (float) $record->stok_minimum => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('is_dijual')
                    ->label('Dijual')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status_stok')
                    ->label('Status stok')
                    ->options([
                        'habis' => 'Habis',
                        'menipis' => 'Menipis',
                        'aman' => 'Aman',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'habis' => $query->where('stok', '<=', 0),
                            'menipis' => $query
                                ->where('stok', '>', 0)
                                ->whereColumn('stok', '<=', 'stok_minimum'),
                            'aman' => $query->whereColumn('stok', '>', 'stok_minimum'),
                            default => $query,
                        };
                    }),
                SelectFilter::make('is_active')
                    ->label('Status aktif')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak aktif',
                    ]),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(fn (): array => MenuBarang::query()
                        ->whereNotNull('kategori')
                        ->distinct()
                        ->orderBy('kategori')
                        ->pluck('kategori', 'kategori')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('stok')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
