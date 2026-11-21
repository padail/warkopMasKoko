<?php

namespace App\Filament\Resources\SerahTerimaKas\Actions;

use App\Models\SerahTerimaKas;
use App\Services\OwnerDashboard\OwnerDashboardService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class VerifikasiSerahTerimaKas
{
    public static function make(): Action
    {
        return Action::make('verifikasi')
            ->label('Verifikasi')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi penerimaan kas')
            ->modalDescription(fn (SerahTerimaKas $record): string => sprintf(
                'Tunai diharapkan: %s. Tunai aktual: %s. Selisih: %s.',
                OwnerDashboardService::formatRupiah((int) $record->tunai_harus_disetor),
                OwnerDashboardService::formatRupiah((int) $record->tunai_aktual),
                OwnerDashboardService::formatRupiah((int) $record->selisih),
            ))
            ->modalSubmitActionLabel('Konfirmasi penerimaan')
            ->visible(fn (SerahTerimaKas $record): bool => (bool) auth()->user()?->isAdmin() && $record->isMenunggu())
            ->authorize(fn (): bool => (bool) auth()->user()?->isAdmin())
            ->databaseTransaction()
            ->action(function (SerahTerimaKas $record): void {
                $updated = DB::transaction(function () use ($record): bool {
                    $handover = SerahTerimaKas::query()
                        ->whereKey($record->getKey())
                        ->lockForUpdate()
                        ->first();

                    if (! $handover || ! $handover->isMenunggu()) {
                        return false;
                    }

                    $handover->update([
                        'diterima_oleh' => auth()->id(),
                        'diverifikasi_pada' => now(),
                        'status' => SerahTerimaKas::STATUS_TERVERIFIKASI,
                    ]);

                    return true;
                });

                if (! $updated) {
                    Notification::make()
                        ->warning()
                        ->title('Laporan sudah terverifikasi.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title('Serah terima kas berhasil diverifikasi.')
                    ->send();
            });
    }
}
