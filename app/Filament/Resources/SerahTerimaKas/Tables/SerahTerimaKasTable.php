<?php

namespace App\Filament\Resources\SerahTerimaKas\Tables;

use App\Filament\Resources\SerahTerimaKas\Actions\VerifikasiSerahTerimaKas;
use App\Filament\Resources\SerahTerimaKas\SerahTerimaKasResource;
use App\Models\SerahTerimaKas;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SerahTerimaKasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_serah')
                    ->label('Tanggal laporan')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable(),
                TextColumn::make('jumlah_transaksi_tunai')
                    ->label('Jumlah transaksi tunai')
                    ->state(fn (SerahTerimaKas $record): int => app(OwnerDashboardService::class)->tunaiTransactionCount($record))
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('tunai_harus_disetor')
                    ->label('Tunai diharapkan')
                    ->money('IDR', locale: 'id')
                    ->alignEnd(),
                TextColumn::make('tunai_aktual')
                    ->label('Tunai aktual')
                    ->money('IDR', locale: 'id')
                    ->alignEnd(),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->money('IDR', locale: 'id')
                    ->badge()
                    ->formatStateUsing(fn (?int $state): string => OwnerDashboardService::formatRupiah($state))
                    ->color(fn (?int $state): string => match (true) {
                        $state > 0 => 'success',
                        $state < 0 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        SerahTerimaKas::STATUS_TERVERIFIKASI => 'Terverifikasi',
                        SerahTerimaKas::STATUS_MENUNGGU => 'Menunggu',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        SerahTerimaKas::STATUS_TERVERIFIKASI => 'success',
                        SerahTerimaKas::STATUS_MENUNGGU => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('diserahkan_pada')
                    ->label('Waktu penyerahan')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('diterimaOleh.name')
                    ->label('Diverifikasi oleh')
                    ->placeholder('-'),
                TextColumn::make('diverifikasi_pada')
                    ->label('Waktu verifikasi')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        SerahTerimaKas::STATUS_MENUNGGU => 'Menunggu',
                        SerahTerimaKas::STATUS_TERVERIFIKASI => 'Terverifikasi',
                    ]),
                Filter::make('rentang_tanggal')
                    ->label('Rentang tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari'),
                        DatePicker::make('sampai')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['dari'] ?? null)) {
                            $query->whereDate('tanggal_serah', '>=', $data['dari']);
                        }
                        if (filled($data['sampai'] ?? null)) {
                            $query->whereDate('tanggal_serah', '<=', $data['sampai']);
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail')
                    ->url(fn (SerahTerimaKas $record): string => SerahTerimaKasResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
                VerifikasiSerahTerimaKas::make(),
            ])
            ->defaultSort('tanggal_serah', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->striped();
    }
}
