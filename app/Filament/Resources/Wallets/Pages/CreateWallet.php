<?php

namespace App\Filament\Resources\Wallets\Pages;

use App\Filament\Resources\Wallets\WalletResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function afterCreate(): void
    {
        $userId = Auth::id();

        if (! $userId) {
            return;
        }

        $this->record->users()->syncWithoutDetaching([$userId]);
    }
}
