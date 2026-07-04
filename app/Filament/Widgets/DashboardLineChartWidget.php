<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Services\TransactionScopeService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class DashboardLineChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Tren Transaksi Bulanan';

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $rows = $this->getTransactionQuery()
            ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as period")
            ->selectRaw("SUM(CASE WHEN transaction_type IN ('topup', 'refund', 'adjustment') THEN amount ELSE 0 END) as income_total")
            ->selectRaw("SUM(CASE WHEN transaction_type = 'payment' THEN amount ELSE 0 END) as expense_total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $rows->pluck('income_total')->map(fn ($value) => round((float) $value, 2))->all(),
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.12)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $rows->pluck('expense_total')->map(fn ($value) => round((float) $value, 2))->all(),
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.08)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
            ],
            'labels' => $rows->pluck('period')
                ->map(fn (string $period): string => \Carbon\Carbon::createFromFormat('Y-m', $period)->translatedFormat('M Y'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
