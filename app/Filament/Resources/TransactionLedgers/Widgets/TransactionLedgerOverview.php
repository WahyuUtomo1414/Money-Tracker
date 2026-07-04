<?php

namespace App\Filament\Resources\TransactionLedgers\Widgets;

use App\Models\TransactionLedger;
use App\Services\TransactionScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionLedgerOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $scopedLedgerQuery = app(TransactionScopeService::class)
            ->scopeTransactionLedgerQuery(TransactionLedger::query());

        $latestLedger = (clone $scopedLedgerQuery)
            ->latest('transaction_date')
            ->latest('id')
            ->first();

        return [
            Stat::make('Total Ledger', number_format((clone $scopedLedgerQuery)->count()))
                ->description('Jumlah histori ledger')
                ->color('primary'),
            Stat::make('Total Perubahan', 'Rp ' . number_format((float) (clone $scopedLedgerQuery)->sum('amount'), 0, ',', '.'))
                ->description('Akumulasi nominal perubahan')
                ->color('success'),
            Stat::make('Saldo Akhir Terbaru', 'Rp ' . number_format((float) ($latestLedger?->end_amount ?? 0), 0, ',', '.'))
                ->description('Diambil dari ledger terbaru')
                ->color('warning'),
        ];
    }
}
