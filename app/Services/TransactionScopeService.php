<?php

namespace App\Services;

use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TransactionScopeService
{
    public function allowedWalletIds(): Collection
    {
        $userId = Auth::id();

        if (! $userId) {
            return collect();
        }

        return UserWallet::query()
            ->where('user_id', $userId)
            ->pluck('wallet_id');
    }

    public function scopeTransactionQuery(Builder $query): Builder
    {
        return $this->scopeByWalletColumn($query, $query->qualifyColumn('wallet_id'));
    }

    public function scopeTransactionLedgerQuery(Builder $query): Builder
    {
        return $this->scopeByWalletColumn($query, $query->qualifyColumn('wallet_id'));
    }

    public function scopeWalletQuery(Builder $query): Builder
    {
        $walletIds = $this->allowedWalletIds()->all();

        if (empty($walletIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->qualifyColumn('id'), $walletIds);
    }

    public function scopeGoalQuery(Builder $query): Builder
    {
        $walletIds = $this->allowedWalletIds()->all();

        if (empty($walletIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->qualifyColumn('wallet_id'), $walletIds);
    }

    protected function scopeByWalletColumn(Builder $query, string $walletColumn): Builder
    {
        $walletIds = $this->allowedWalletIds()->all();

        if (empty($walletIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($walletColumn, $walletIds);
    }
}
