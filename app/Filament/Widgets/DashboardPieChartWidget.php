<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Services\TransactionScopeService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class DashboardPieChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Distribusi Tipe Transaksi';

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $totals = $this->getTransactionQuery()
            ->selectRaw('transaction_type, SUM(amount) as total_amount')
            ->groupBy('transaction_type')
            ->pluck('total_amount', 'transaction_type');

        $types = [
            TransactionTypeEnum::Topup,
            TransactionTypeEnum::Payment,
            TransactionTypeEnum::Refund,
            TransactionTypeEnum::Adjustment,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Nominal',
                    'data' => collect($types)
                        ->map(fn (TransactionTypeEnum $type) => round((float) ($totals[$type->value] ?? 0), 2))
                        ->all(),
                    'backgroundColor' => [
                        '#16a34a',
                        '#dc2626',
                        '#0ea5e9',
                        '#f59e0b',
                    ],
                ],
            ],
            'labels' => collect($types)
                ->map(fn (TransactionTypeEnum $type) => $type->label())
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
}
