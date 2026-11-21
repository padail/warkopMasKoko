<?php

namespace App\Filament\Pages;

use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OwnerDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard Owner';

    protected static ?string $navigationLabel = 'Dashboard';

    protected ?string $subheading = 'Ringkasan penjualan dan verifikasi kas seluruh karyawan.';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('periode')
                    ->label('Periode laporan')
                    ->options(OwnerDashboardService::periodOptions())
                    ->default(OwnerDashboardService::PRESET_HARI_INI)
                    ->selectablePlaceholder(false)
                    ->live(),
                DatePicker::make('start')
                    ->label('Tanggal mulai')
                    ->native(false)
                    ->visible(fn (Get $get): bool => $get('periode') === OwnerDashboardService::PRESET_RENTANG)
                    ->required(fn (Get $get): bool => $get('periode') === OwnerDashboardService::PRESET_RENTANG),
                DatePicker::make('end')
                    ->label('Tanggal akhir')
                    ->native(false)
                    ->visible(fn (Get $get): bool => $get('periode') === OwnerDashboardService::PRESET_RENTANG)
                    ->required(fn (Get $get): bool => $get('periode') === OwnerDashboardService::PRESET_RENTANG),
            ]);
    }
}
