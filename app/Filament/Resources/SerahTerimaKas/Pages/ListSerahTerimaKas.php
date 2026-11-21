<?php

namespace App\Filament\Resources\SerahTerimaKas\Pages;

use App\Filament\Resources\SerahTerimaKas\SerahTerimaKasResource;
use Filament\Resources\Pages\ListRecords;

class ListSerahTerimaKas extends ListRecords
{
    protected static string $resource = SerahTerimaKasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
