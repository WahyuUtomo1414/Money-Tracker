<?php

namespace App\Filament\Resources\TransactionLedgers\Pages;

use App\Filament\Resources\TransactionLedgers\TransactionLedgerResource;
use App\Filament\Resources\TransactionLedgers\Widgets\TransactionLedgerOverview;
use App\Models\Transaction;
use App\Services\TransactionScopeService;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransactionLedgers extends ListRecords
{
    protected static string $resource = TransactionLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionLedgerOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'topup' => Tab::make('Topup')
                ->badge((string) $this->countByTransactionType('topup'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph(
                    'reference',
                    [Transaction::class],
                    fn (Builder $query) => $query->where('transaction_type', 'topup'),
                )),
            'payment' => Tab::make('Payment')
                ->badge((string) $this->countByTransactionType('payment'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph(
                    'reference',
                    [Transaction::class],
                    fn (Builder $query) => $query->where('transaction_type', 'payment'),
                )),
            'refund' => Tab::make('Refund')
                ->badge((string) $this->countByTransactionType('refund'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph(
                    'reference',
                    [Transaction::class],
                    fn (Builder $query) => $query->where('transaction_type', 'refund'),
                )),
            'adjustment' => Tab::make('Adjustment')
                ->badge((string) $this->countByTransactionType('adjustment'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph(
                    'reference',
                    [Transaction::class],
                    fn (Builder $query) => $query->where('transaction_type', 'adjustment'),
                )),
        ];
    }

    protected function countByTransactionType(string $type): int
    {
        return app(TransactionScopeService::class)
            ->scopeTransactionQuery(Transaction::query())
            ->where('transaction_type', $type)
            ->count();
    }
}
