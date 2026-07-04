<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionTypeEnum;
use App\Services\TransactionScopeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([
                        Hidden::make('uuid')
                            ->default(fn (): string => (string) Str::uuid()),
                        TextInput::make('transaction_no')
                            ->label('Nomor Transaksi')
                            ->helperText('Nomor transaksi diisi otomatis oleh service.')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->options(TransactionTypeEnum::options())
                            ->required(),
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->prefix('Rp')
                            ->inputMode('numeric')
                            ->formatStateUsing(fn ($state): ?string => filled($state) ? number_format((int) $state, 0, ',', '.') : null)
                            ->afterStateUpdatedJs(<<<'JS'
                                let value = ($state ?? '').toString().replace(/\D/g, '')
                                $set('amount', value ? new Intl.NumberFormat('id-ID').format(Number(value)) : null)
                            JS)
                            ->dehydrateStateUsing(fn (?string $state): ?int => filled($state) ? (int) str_replace('.', '', $state) : null)
                            ->required()
                            ->rule('integer'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Bukti Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('transactions'),
                        Select::make('wallet_id')
                            ->label('Wallet')
                            ->relationship('wallet', 'account_name', fn ($query) => app(TransactionScopeService::class)->scopeWalletQuery($query))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', fn ($query) => $query->where('type', 'transaction'))
                            ->searchable()
                            ->preload(),
                        Select::make('goal_id')
                            ->label('Target Tabungan')
                            ->relationship('goal', 'name', fn ($query) => app(TransactionScopeService::class)->scopeGoalQuery($query))
                            ->searchable()
                            ->preload(),
                        Hidden::make('active')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
