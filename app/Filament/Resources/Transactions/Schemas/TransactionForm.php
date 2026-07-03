<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('uuid')
                    ->default(fn (): string => (string) Str::uuid()),
                TextInput::make('transaction_no')
                    ->label('Nomor Transaksi')
                    ->helperText('Nomor transaksi diisi otomatis oleh service.')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('transaction_type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'topup' => 'Topup',
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                    ])
                    ->required(),
                DatePicker::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->required(),
                TextInput::make('amount')
                    ->label('Nominal')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Bukti Gambar')
                    ->image()
                    ->directory('transactions'),
                Select::make('wallet_id')
                    ->label('Wallet')
                    ->relationship('wallet', 'account_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('goal_id')
                    ->label('Target Tabungan')
                    ->relationship('goal', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
            ]);
    }
}
