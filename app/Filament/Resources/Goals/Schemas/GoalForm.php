<?php

namespace App\Filament\Resources\Goals\Schemas;

use App\Services\TransactionScopeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Target Tabungan')
                    ->schema([
                        Select::make('wallet_id')
                            ->label('Wallet')
                            ->relationship('wallet', 'account_name', fn ($query) => app(TransactionScopeService::class)->scopeWalletQuery($query))
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Target')
                            ->required()
                            ->maxLength(128),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        TextInput::make('target_amount')
                            ->label('Target Nominal')
                            ->prefix('Rp')
                            ->inputMode('numeric')
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state): ?string => filled($state) ? number_format((int) $state, 0, ',', '.') : null)
                            ->afterStateUpdatedJs(<<<'JS'
                                let value = ($state ?? '').toString().replace(/\D/g, '')
                                $set('target_amount', value ? new Intl.NumberFormat('id-ID').format(Number(value)) : null)
                            JS)
                            ->mutateStateForValidationUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->dehydrateStateUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->required()
                            ->rule('integer'),
                        DatePicker::make('target_date')
                            ->label('Target Tanggal')
                            ->required(),
                        Toggle::make('active')
                            ->label('Aktif')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
