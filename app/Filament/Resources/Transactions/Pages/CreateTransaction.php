<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Helpers\TransactionNoHelper;
use App\Services\TransactionLedgerService;
use App\Services\TransactionReceiptService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['transaction_no'] = TransactionNoHelper::generate(
            TransactionTypeEnum::from($data['transaction_type']),
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        app(TransactionLedgerService::class)->create($this->record);

        try {
            app(TransactionReceiptService::class)->sendCreatedReceipt($this->record);
        } catch (\Throwable $throwable) {
            report($throwable);

            Notification::make()
                ->warning()
                ->title('Transaksi berhasil disimpan')
                ->body('Email bukti transaksi belum berhasil dikirim. Silakan cek konfigurasi email atau generator PDF.')
                ->send();
        }
    }
}
