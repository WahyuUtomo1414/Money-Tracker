<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Services\TransactionReceiptService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_email')
                ->label('Kirim Email')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Kirim ulang email transaksi?')
                ->modalDescription('Email bukti transaksi dan PDF akan dikirim ke semua pengguna yang di-assign ke rekening transaksi ini.')
                ->modalSubmitActionLabel('Kirim Email')
                ->action(function (): void {
                    try {
                        $recipients = app(TransactionReceiptService::class)->sendCreatedReceipt($this->getRecord());
                    } catch (\Throwable $throwable) {
                        report($throwable);

                        Notification::make()
                            ->danger()
                            ->title('Email gagal dikirim')
                            ->body('Silakan cek konfigurasi email atau generator PDF.')
                            ->send();

                        return;
                    }

                    if (empty($recipients)) {
                        Notification::make()
                            ->warning()
                            ->title('Email tidak dikirim')
                            ->body('Rekening transaksi ini belum memiliki pengguna dengan email.')
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title('Email berhasil dikirim')
                        ->body('Bukti transaksi dikirim ke: ' . implode(', ', $recipients))
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
