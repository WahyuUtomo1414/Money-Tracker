<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Services\TransactionScopeService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardStatsOverviewWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $transactionQuery = $this->getTransactionQuery();
        $ledgerQuery = $this->getLedgerQuery();

        $totalTransactions = (clone $transactionQuery)->count();
        $totalIncome = (clone $transactionQuery)
            ->whereIn('transaction_type', ['topup', 'refund', 'adjustment'])
            ->sum('amount');
        $totalExpense = (clone $transactionQuery)
            ->where('transaction_type', 'payment')
            ->sum('amount');
        $latestBalance = (clone $ledgerQuery)
            ->latest('transaction_date')
            ->latest('id')
            ->value('end_amount') ?? 0;

        return [
            Stat::make('Total Transaksi', number_format($totalTransactions))
                ->description('Total Transaksi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([4, 6, 5, 8, 7, 10, 12])
                ->color('primary'),
            Stat::make('Total Pemasukan', 'Rp ' . number_format((float) $totalIncome, 0, ',', '.'))
                ->description('Total Top Up, Penyesuaian, dan Pengembalian Dana')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([3, 5, 4, 9, 8, 11, 14])
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format((float) $totalExpense, 0, ',', '.'))
                ->description('Total Pembayaran')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([12, 10, 11, 8, 9, 6, 5])
                ->color('danger'),
            Stat::make('Saldo Terakhir', 'Rp ' . number_format((float) $latestBalance, 0, ',', '.'))
                ->description('Saldo Akhir')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([2, 4, 6, 5, 9, 11, 13])
                ->color('info'),
        ];
    }

    protected function getTransactionQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeTransactionQuery(Transaction::query());

        if (filled($this->pageFilters['wallet_id'] ?? null)) {
            $query->where('wallet_id', $this->pageFilters['wallet_id']);
        }

        if (filled($this->pageFilters['transaction_type'] ?? null)) {
            $query->where('transaction_type', $this->pageFilters['transaction_type']);
        }

        if (filled($this->pageFilters['start_date'] ?? null)) {
            $query->whereDate('transaction_date', '>=', $this->pageFilters['start_date']);
        }

        if (filled($this->pageFilters['end_date'] ?? null)) {
            $query->whereDate('transaction_date', '<=', $this->pageFilters['end_date']);
        }

        return $query;
    }

    protected function getLedgerQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeTransactionLedgerQuery(TransactionLedger::query());

        if (filled($this->pageFilters['wallet_id'] ?? null)) {
            $query->where('wallet_id', $this->pageFilters['wallet_id']);
        }

        if (filled($this->pageFilters['transaction_type'] ?? null)) {
            $query->where('transaction_type', $this->pageFilters['transaction_type']);
        }

        if (filled($this->pageFilters['start_date'] ?? null)) {
            $query->whereDate('transaction_date', '>=', $this->pageFilters['start_date']);
        }

        if (filled($this->pageFilters['end_date'] ?? null)) {
            $query->whereDate('transaction_date', '<=', $this->pageFilters['end_date']);
        }

        return $query;
    }
}
