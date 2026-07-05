<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\Goals\GoalResource;
use App\Models\Goal;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class DashboardGoalsWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.widgets.dashboard-goals-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return GoalResource::getEloquentQuery()->exists();
    }

    protected function getViewData(): array
    {
        $query = GoalResource::getEloquentQuery()
            ->with([
                'wallet',
                'transactions:id,goal_id,transaction_type,amount',
            ]);

        if (filled($this->pageFilters['wallet_id'] ?? null)) {
            $query->where('wallet_id', $this->pageFilters['wallet_id']);
        }

        $totalGoals = (clone $query)->count();

        $goals = $query
            ->orderBy('target_date')
            ->limit(5)
            ->get()
            ->map(function (Goal $goal): array {
                $currentAmount = $goal->transactions->sum(function ($transaction): float {
                    $amount = (float) $transaction->amount;

                    return $transaction->transaction_type === TransactionTypeEnum::Payment->value
                        ? $amount * -1
                        : $amount;
                });

                $targetAmount = (float) $goal->target_amount;
                $progress = $targetAmount > 0
                    ? min(100, max(0, round(($currentAmount / $targetAmount) * 100)))
                    : 0;

                return [
                    'id' => $goal->id,
                    'wallet' => $goal->wallet?->display_name ?? '-',
                    'name' => $goal->name,
                    'target_amount' => $targetAmount,
                    'current_amount' => $currentAmount,
                    'target_date' => $goal->target_date?->format('d M Y') ?? '-',
                    'progress' => $progress,
                    'active' => (bool) $goal->active,
                ];
            });

        return [
            'goals' => $goals,
            'totalGoals' => $totalGoals,
        ];
    }
}
