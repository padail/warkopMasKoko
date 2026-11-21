<?php

namespace App\Filament\Resources\SerahTerimaKas;

use App\Filament\Resources\SerahTerimaKas\Pages\ListSerahTerimaKas;
use App\Filament\Resources\SerahTerimaKas\Pages\ViewSerahTerimaKas;
use App\Filament\Resources\SerahTerimaKas\Tables\SerahTerimaKasTable;
use App\Models\SerahTerimaKas;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SerahTerimaKasResource extends Resource
{
    protected static ?string $model = SerahTerimaKas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Kas';

    protected static ?string $navigationLabel = 'Serah Terima Kas';

    protected static ?string $modelLabel = 'serah terima kas';

    protected static ?string $pluralModelLabel = 'serah terima kas';

    protected static ?string $recordTitleAttribute = 'tanggal_serah';

    public static function table(Table $table): Table
    {
        return SerahTerimaKasTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ringkasan Kas')
                ->columns(2)
                ->schema([
                    TextEntry::make('tanggal_serah')->label('Tanggal laporan')->date('d/m/Y'),
                    TextEntry::make('user.name')->label('Karyawan')->placeholder('-'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            SerahTerimaKas::STATUS_TERVERIFIKASI => 'Terverifikasi',
                            SerahTerimaKas::STATUS_MENUNGGU => 'Menunggu verifikasi',
                            default => (string) $state,
                        })
                        ->color(fn (?string $state): string => match ($state) {
                            SerahTerimaKas::STATUS_TERVERIFIKASI => 'success',
                            SerahTerimaKas::STATUS_MENUNGGU => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('diserahkan_pada')->label('Waktu penyerahan')->dateTime('d/m/Y H:i')->placeholder('-'),
                    TextEntry::make('diterimaOleh.name')->label('Diverifikasi oleh')->placeholder('-'),
                    TextEntry::make('diverifikasi_pada')->label('Waktu verifikasi')->dateTime('d/m/Y H:i')->placeholder('-'),
                ]),
            Section::make('Rincian Tunai')
                ->columns(3)
                ->schema([
                    TextEntry::make('tunai_harus_disetor')->label('Harus disetor')->money('IDR', locale: 'id'),
                    TextEntry::make('tunai_aktual')->label('Aktual diserahkan')->money('IDR', locale: 'id'),
                    TextEntry::make('selisih_tunai')->label('Selisih tunai')->money('IDR', locale: 'id')
                        ->color(fn (?int $state): string => match (true) {
                            $state > 0 => 'success',
                            $state < 0 => 'danger',
                            default => 'gray',
                        }),
                ]),
            Section::make('Rincian Non-Tunai')
                ->columns(3)
                ->schema([
                    TextEntry::make('non_tunai_harus_disetor')->label('Harus disetor')->money('IDR', locale: 'id'),
                    TextEntry::make('non_tunai_aktual')->label('Aktual diserahkan')->money('IDR', locale: 'id'),
                    TextEntry::make('selisih_non_tunai')->label('Selisih non-tunai')->money('IDR', locale: 'id')
                        ->color(fn (?int $state): string => match (true) {
                            $state > 0 => 'success',
                            $state < 0 => 'danger',
                            default => 'gray',
                        }),
                ]),
            Section::make('Catatan Karyawan')
                ->schema([
                    TextEntry::make('catatan')
                        ->label('Catatan')
                        ->placeholder('Tidak ada catatan.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSerahTerimaKas::route('/'),
            'view' => ViewSerahTerimaKas::route('/{record}'),
        ];
    }
}
