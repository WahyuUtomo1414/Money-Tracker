<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Helpers\TransactionNoHelper;
use App\Services\TransactionLedgerService;
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
    }
}
