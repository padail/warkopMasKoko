<?php

namespace App\Filament\Resources\SerahTerimaKas\Pages;

use App\Filament\Resources\SerahTerimaKas\Actions\VerifikasiSerahTerimaKas;
use App\Filament\Resources\SerahTerimaKas\SerahTerimaKasResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSerahTerimaKas extends ViewRecord
{
    protected static string $resource = SerahTerimaKasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            VerifikasiSerahTerimaKas::make(),
        ];
    }
}
