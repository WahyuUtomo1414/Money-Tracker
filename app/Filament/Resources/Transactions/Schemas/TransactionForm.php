<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionTypeEnum;
use App\Models\TransactionLedger;
use App\Services\TransactionScopeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required(),
                        Select::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->options(TransactionTypeEnum::options())
                            ->required(),
                        Select::make('wallet_id')
                            ->label('Wallet')
                            ->relationship('wallet', 'account_name', fn ($query) => app(TransactionScopeService::class)->scopeWalletQuery($query))
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                            ->helperText(function (Get $get): ?string {
                                $walletId = $get('wallet_id');

                                if (! filled($walletId)) {
                                    return 'Pilih wallet untuk melihat saldo saat ini.';
                                }

                                $balance = static::getWalletBalance($walletId);

                                return 'Saldo wallet saat ini: Rp ' . number_format((float) $balance, 0, ',', '.');
                            })
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->prefix('Rp')
                            ->inputMode('numeric')
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state): ?string => filled($state) ? number_format((int) $state, 0, ',', '.') : null)
                            ->afterStateUpdatedJs(<<<'JS'
                                let value = ($state ?? '').toString().replace(/\D/g, '')
                                $set('amount', value ? new Intl.NumberFormat('id-ID').format(Number(value)) : null)
                            JS)
                            ->mutateStateForValidationUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->dehydrateStateUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->required()
                            ->rule('integer')
                            ->rule(function (Get $get): \Closure {
                                return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                    if ($get('transaction_type') !== TransactionTypeEnum::Payment->value) {
                                        return;
                                    }

                                    $walletId = $get('wallet_id');

                                    if (! filled($walletId) || ! filled($value)) {
                                        return;
                                    }

                                    $balance = static::getWalletBalance($walletId);

                                    if ((int) $value > $balance) {
                                        $fail('Nominal payment tidak boleh melebihi saldo wallet yang dipilih.');
                                    }
                                };
                            }),
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
                        FileUpload::make('image')
                            ->label('Bukti Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('transactions'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Hidden::make('active')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function getWalletBalance(int | string | null $walletId): float
    {
        if (! filled($walletId)) {
            return 0;
        }

        return (float) (TransactionLedger::query()
            ->where('wallet_id', $walletId)
            ->latest('transaction_date')
            ->latest('id')
            ->value('end_amount') ?? 0);
    }
}
