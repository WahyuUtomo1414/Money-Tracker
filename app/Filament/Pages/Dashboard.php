<?php

namespace App\Filament\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Widgets\DashboardLineChartWidget;
use App\Filament\Widgets\DashboardPieChartWidget;
use App\Filament\Widgets\DashboardStatsOverviewWidget;
use App\Filament\Widgets\DashboardGoalsWidget;
use App\Services\TransactionScopeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    protected static ?int $sort = 2;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter Dashboard')
                    ->schema([
                        Select::make('wallet_id')
                            ->label('Rekening')
                            ->options(fn (): array => app(TransactionScopeService::class)
                                ->scopeWalletQuery(\App\Models\Wallet::query())
                                ->get()
                                ->mapWithKeys(fn ($wallet): array => [
                                    $wallet->id => $wallet->display_name,
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->placeholder('Semua rekening'),
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false),
                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->native(false),
                        Select::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->options(TransactionTypeEnum::options())
                            ->searchable()
                            ->placeholder('Semua tipe'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverviewWidget::class,
            //DashboardGoalsWidget::class,
            DashboardLineChartWidget::class,
            DashboardPieChartWidget::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }
}
