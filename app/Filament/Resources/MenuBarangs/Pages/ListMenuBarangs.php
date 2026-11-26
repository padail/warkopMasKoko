<?php

namespace App\Filament\Resources\MenuBarangs\Pages;

use App\Filament\Resources\MenuBarangs\MenuBarangResource;
use Filament\Resources\Pages\ListRecords;

class ListMenuBarangs extends ListRecords
{
    protected static string $resource = MenuBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
