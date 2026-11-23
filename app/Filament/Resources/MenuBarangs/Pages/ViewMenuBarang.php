<?php

namespace App\Filament\Resources\MenuBarangs\Pages;

use App\Filament\Resources\MenuBarangs\MenuBarangResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMenuBarang extends ViewRecord
{
    protected static string $resource = MenuBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
