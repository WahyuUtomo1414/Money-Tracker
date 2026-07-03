<?php

namespace App\Filament\Resources\Wallets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WalletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('bank_name')
                    ->label('Nama Bank')
                    ->required()
                    ->maxLength(128),
                TextInput::make('account_no')
                    ->label('Nomor Rekening')
                    ->required()
                    ->maxLength(128),
                TextInput::make('account_name')
                    ->label('Nama Pemilik Rekening')
                    ->required()
                    ->maxLength(128),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
            ]);
    }
}
