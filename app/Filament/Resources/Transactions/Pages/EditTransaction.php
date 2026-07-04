<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionLedgerService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected ?Transaction $oldTransaction = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (): void {
                    $this->oldTransaction = $this->getRecord()->replicate();
                    $this->oldTransaction->id = $this->getRecord()->id;
                    $this->oldTransaction->wallet_id = $this->getRecord()->wallet_id;
                    $this->oldTransaction->category_id = $this->getRecord()->category_id;
                    $this->oldTransaction->goal_id = $this->getRecord()->goal_id;
                    $this->oldTransaction->transaction_no = $this->getRecord()->transaction_no;
                    $this->oldTransaction->transaction_type = $this->getRecord()->transaction_type;
                    $this->oldTransaction->transaction_date = $this->getRecord()->transaction_date;
                    $this->oldTransaction->amount = $this->getRecord()->amount;
                })
                ->after(fn () => $this->oldTransaction ? app(TransactionLedgerService::class)->delete($this->oldTransaction) : null),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->oldTransaction = $this->getRecord()->replicate();
        $this->oldTransaction->id = $this->getRecord()->id;
        $this->oldTransaction->wallet_id = $this->getRecord()->wallet_id;
        $this->oldTransaction->category_id = $this->getRecord()->category_id;
        $this->oldTransaction->goal_id = $this->getRecord()->goal_id;
        $this->oldTransaction->transaction_no = $this->getRecord()->transaction_no;
        $this->oldTransaction->transaction_type = $this->getRecord()->transaction_type;
        $this->oldTransaction->transaction_date = $this->getRecord()->transaction_date;
        $this->oldTransaction->amount = $this->getRecord()->amount;
    }

    protected function afterSave(): void
    {
        if (! $this->oldTransaction) {
            return;
        }

        app(TransactionLedgerService::class)->update($this->oldTransaction, $this->getRecord());
    }
}
