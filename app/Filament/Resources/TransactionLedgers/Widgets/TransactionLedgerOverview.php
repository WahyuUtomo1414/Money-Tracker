<?php

namespace App\Filament\Resources\TransactionLedgers\Widgets;

use App\Models\TransactionLedger;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionLedgerOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $latestLedger = TransactionLedger::query()
            ->latest('transaction_date')
            ->latest('id')
            ->first();

        return [
            Stat::make('Total Ledger', number_format(TransactionLedger::count()))
                ->description('Jumlah histori ledger')
                ->color('primary'),
            Stat::make('Total Perubahan', 'Rp ' . number_format((float) TransactionLedger::sum('amount'), 0, ',', '.'))
                ->description('Akumulasi nominal perubahan')
                ->color('success'),
            Stat::make('Saldo Akhir Terbaru', 'Rp ' . number_format((float) ($latestLedger?->end_amount ?? 0), 0, ',', '.'))
                ->description('Diambil dari ledger terbaru')
                ->color('warning'),
        ];
    }
}
