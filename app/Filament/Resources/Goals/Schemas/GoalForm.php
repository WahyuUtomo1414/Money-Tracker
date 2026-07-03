<?php

namespace App\Filament\Resources\Goals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('wallet_id')
                    ->label('Wallet')
                    ->relationship('wallet', 'account_name')
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
                    ->required()
                    ->numeric(),
                DatePicker::make('target_date')
                    ->label('Target Tanggal')
                    ->required(),
                Toggle::make('active')
                    ->label('Aktif')
                    ->required()
                    ->default(true),
            ]);
    }
}
