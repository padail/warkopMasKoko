<?php

namespace App\Filament\Resources\MenuBarangs;

use App\Filament\Resources\MenuBarangs\Pages\ListMenuBarangs;
use App\Filament\Resources\MenuBarangs\Pages\ViewMenuBarang;
use App\Filament\Resources\MenuBarangs\Tables\MenuBarangsTable;
use App\Models\MenuBarang;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MenuBarangResource extends Resource
{
    protected static ?string $model = MenuBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Inventori';

    protected static ?string $navigationLabel = 'Pantau Stok';

    protected static ?string $modelLabel = 'barang';

    protected static ?string $pluralModelLabel = 'stok barang';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function table(Table $table): Table
    {
        return MenuBarangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Barang')
                ->columns(2)
                ->schema([
                    TextEntry::make('nama')->label('Nama barang'),
                    TextEntry::make('kategori')->label('Kategori')->badge()->color('gray'),
                    TextEntry::make('satuan')->label('Satuan'),
                    TextEntry::make('harga_jual')->label('Harga jual')->money('IDR', locale: 'id'),
                ]),
            Section::make('Status Stok')
                ->columns(3)
                ->schema([
                    TextEntry::make('stok')->label('Stok saat ini')->numeric(decimalPlaces: 3),
                    TextEntry::make('stok_minimum')->label('Stok minimum')->numeric(decimalPlaces: 3),
                    TextEntry::make('status_stok')
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
                ]),
        ]);
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
            'index' => ListMenuBarangs::route('/'),
            'view' => ViewMenuBarang::route('/{record}'),
        ];
    }
}
