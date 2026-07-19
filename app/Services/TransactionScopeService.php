<?php

namespace App\Services;

use App\Models\User;
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
        return $this->scopeByOwnerOrWalletColumn($query, $query->qualifyColumn('wallet_id'));
    }

    public function scopeTransactionLedgerQuery(Builder $query): Builder
    {
        return $this->scopeByOwnerOrWalletColumn($query, $query->qualifyColumn('wallet_id'));
    }

    public function scopeWalletQuery(Builder $query): Builder
    {
        if ($this->canSeeAllData()) {
            return $query;
        }

        $userId = Auth::id();
        $walletIds = $this->allowedWalletIds()->all();

        if (! $userId && empty($walletIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $query) use ($userId, $walletIds): void {
            if ($userId) {
                $query->where($query->qualifyColumn('created_by'), $userId);
            }

            if (! empty($walletIds)) {
                $query->orWhereIn($query->qualifyColumn('id'), $walletIds);
            }
        });
    }

    public function scopeGoalQuery(Builder $query): Builder
    {
        if ($this->canSeeAllData()) {
            return $query;
        }

        return $this->scopeByOwnerOrWalletColumn($query, $query->qualifyColumn('wallet_id'));
    }

    protected function scopeByOwnerOrWalletColumn(Builder $query, string $walletColumn): Builder
    {
        if ($this->canSeeAllData()) {
            return $query;
        }

        $userId = Auth::id();
        $walletIds = $this->allowedWalletIds()->all();

        if (! $userId && empty($walletIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $query) use ($userId, $walletIds, $walletColumn): void {
            if ($userId) {
                $query->where($query->qualifyColumn('created_by'), $userId);
            }

            if (! empty($walletIds)) {
                $query->orWhereIn($walletColumn, $walletIds);
            }
        });
    }

    protected function canSeeAllData(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->isSuperAdmin() ?? false;
    }
}
